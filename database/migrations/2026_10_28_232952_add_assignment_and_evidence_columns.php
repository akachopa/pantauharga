<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('point_assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('point_assignments', 'business_id')) {
                $table->unsignedBigInteger('business_id')->nullable()->after('point_id');
            }
            if (! Schema::hasColumn('point_assignments', 'report_days')) {
                $table->string('report_days', 60)->nullable()->after('ends_on');
            }
        });

        Schema::table('reports', function (Blueprint $table) {
            if (! Schema::hasColumn('reports', 'evidence_path')) {
                $table->string('evidence_path', 255)->nullable()->after('notes');
            }
        });

        Schema::table('action_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('action_plans', 'evidence_path')) {
                $table->string('evidence_path', 255)->nullable()->after('notes');
            }
        });

        Schema::table('community_price_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('community_price_reports', 'review_note')) {
                $table->string('review_note', 500)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('point_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('point_assignments', 'business_id')) {
                $table->dropColumn('business_id');
            }
            if (Schema::hasColumn('point_assignments', 'report_days')) {
                $table->dropColumn('report_days');
            }
        });

        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'evidence_path')) {
                $table->dropColumn('evidence_path');
            }
        });

        Schema::table('action_plans', function (Blueprint $table) {
            if (Schema::hasColumn('action_plans', 'evidence_path')) {
                $table->dropColumn('evidence_path');
            }
        });

        Schema::table('community_price_reports', function (Blueprint $table) {
            if (Schema::hasColumn('community_price_reports', 'review_note')) {
                $table->dropColumn('review_note');
            }
        });
    }
};
