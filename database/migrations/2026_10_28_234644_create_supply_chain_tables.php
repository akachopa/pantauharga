<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('point_id')->constrained('monitoring_points');
            $table->foreignId('commodity_id')->nullable()->constrained('commodities');
            $table->string('notes', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('supply_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_link_id')->constrained('supply_links')->cascadeOnDelete();
            $table->date('scheduled_on');
            $table->decimal('quantity', 14, 2);
            $table->string('unit', 32)->default('kg');
            $table->enum('status', ['planned', 'in_transit', 'delivered', 'cancelled'])->default('planned');
            $table->string('notes', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_shipments');
        Schema::dropIfExists('supply_links');
    }
};
