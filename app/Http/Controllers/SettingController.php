<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\MonitoringPoint;
use App\Models\Organization;
use App\Models\PointAssignment;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'pengguna');
        $selectedUserId = $request->get('user', 1);

        $users = User::with(['roles', 'organization'])->orderBy('id')->get();
        $selectedSettingsUser = $users->firstWhere('id', (int) $selectedUserId) ?? $users->first();
        $roles = Role::all();
        $organizations = Organization::all();
        $markets = Market::query()->orderBy('name')->get(['id', 'name']);
        $points = MonitoringPoint::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $assignedPointIds = $selectedSettingsUser
            ? $this->activeAssignments($selectedSettingsUser->id)->pluck('point_id')->map(fn ($id): int => (int) $id)->all()
            : [];
        $regions = Region::query()->orderBy('name')->get(['id', 'name']);

        $accessLogs = DB::table('access_change_logs')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $auditLogs = DB::table('audit_logs as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.actor_id')
            ->orderByDesc('a.created_at')
            ->limit(40)
            ->select('a.*', 'u.name as actor_name')
            ->get();

        $scopeCount = DB::table('user_data_scopes')->count();
        $permissionCount = DB::table('permissions')->count();

        $notificationPrefs = session('notification_prefs', [
            'stock_low' => true,
            'report_missing' => true,
            'need_verify' => true,
            'policy_trigger' => true,
        ]);

        return view('settings.index', compact(
            'activeTab',
            'users',
            'selectedSettingsUser',
            'selectedUserId',
            'roles',
            'organizations',
            'markets',
            'points',
            'assignedPointIds',
            'regions',
            'accessLogs',
            'auditLogs',
            'scopeCount',
            'permissionCount',
            'notificationPrefs'
        ));
    }

    public function updateNotificationPrefs(Request $request)
    {
        $validated = $request->validate([
            'stock_low' => 'nullable|boolean',
            'report_missing' => 'nullable|boolean',
            'need_verify' => 'nullable|boolean',
            'policy_trigger' => 'nullable|boolean',
        ]);

        session([
            'notification_prefs' => [
                'stock_low' => $request->boolean('stock_low'),
                'report_missing' => $request->boolean('report_missing'),
                'need_verify' => $request->boolean('need_verify'),
                'policy_trigger' => $request->boolean('policy_trigger'),
            ],
        ]);

        return redirect()->route('settings.index', ['tab' => 'notifikasi'])
            ->with('success', 'Preferensi notifikasi berhasil disimpan untuk sesi ini.');
    }

    public function switchUser($id)
    {
        abort_unless(config('auth.demo_auto_login'), 404);

        $user = User::findOrFail($id);
        Auth::login($user);
        session(['simulated_user_id' => $user->id]);

        if ($user->hasRole('mitra')) {
            return redirect()->route('mitra.dashboard')->with('success', "Beralih peran aktif sebagai: {$user->name} ({$user->roleName()})");
        }

        return redirect()->back()->with('success', "Beralih peran aktif sebagai: {$user->name} ({$user->roleName()})");
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nip' => $request->nip,
            'phone' => $request->phone,
            'organization_id' => $request->organization_id ?? 1,
            'is_active' => true,
            'is_internal' => in_array($request->role_id, [1, 2, 3, 4]),
            'is_demo' => false,
        ]);

        $user->roles()->sync([$request->role_id]);

        return redirect()->route('settings.index', ['tab' => 'pengguna'])
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan!");
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role_id' => 'required|exists:roles,id',
            'point_ids' => 'nullable|array',
            'point_ids.*' => 'integer|exists:monitoring_points,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'phone' => $request->phone,
            'organization_id' => $request->organization_id,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->roles()->sync([$request->role_id]);
        $user->update(['is_internal' => in_array((int) $request->role_id, [1, 2, 3, 4], true)]);

        if ($request->boolean('assign_points')) {
            $this->syncPointAssignments($user->id, array_map('intval', $request->input('point_ids', [])));
        }

        return redirect()->route('settings.index', ['tab' => 'pengguna', 'user' => $user->id])
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui!");
    }

    public function destroyUser($id)
    {
        if ($id == 1) {
            return redirect()->back()->with('error', 'Administrator utama tidak dapat dihapus!');
        }
        User::destroy($id);

        return redirect()->route('settings.index', ['tab' => 'pengguna'])->with('success', 'Pengguna berhasil dihapus!');
    }

    /**
     * @return Collection<int, PointAssignment>
     */
    private function activeAssignments(int $userId): Collection
    {
        $today = now()->toDateString();

        return PointAssignment::query()
            ->where('user_id', $userId)
            ->where('starts_on', '<=', $today)
            ->where(fn ($query) => $query->whereNull('ends_on')->orWhere('ends_on', '>=', $today))
            ->get();
    }

    /**
     * Start assignments for newly checked points and end the ones that were unchecked.
     *
     * @param  list<int>  $pointIds
     */
    private function syncPointAssignments(int $userId, array $pointIds): void
    {
        $active = $this->activeAssignments($userId);
        $yesterday = now()->subDay()->toDateString();

        foreach ($active->whereNotIn('point_id', $pointIds) as $assignment) {
            if ($assignment->starts_on->toDateString() > $yesterday) {
                $assignment->delete();
            } else {
                $assignment->update(['ends_on' => $yesterday]);
            }
        }

        foreach (array_diff($pointIds, $active->pluck('point_id')->map(fn ($id): int => (int) $id)->all()) as $pointId) {
            PointAssignment::create([
                'user_id' => $userId,
                'point_id' => $pointId,
                'starts_on' => now()->toDateString(),
            ]);
        }
    }
}
