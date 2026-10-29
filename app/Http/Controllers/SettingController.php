<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\MonitoringPoint;
use App\Models\Organization;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
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
        $points = MonitoringPoint::query()->orderBy('name')->get(['id', 'name']);
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
        $user = User::findOrFail($id);
        Auth::login($user);
        session(['simulated_user_id' => $user->id]);

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

        return redirect()->route('settings.index', ['tab' => 'pengguna'])
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
}
