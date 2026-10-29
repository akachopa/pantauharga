<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_price_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_id')->constrained('commodities');
            $table->foreignId('market_id')->constrained('markets');
            $table->decimal('reported_price', 16, 2);
            $table->decimal('official_price', 16, 2)->nullable();
            $table->string('reporter_name', 120);
            $table->string('phone', 30)->nullable();
            $table->string('note', 500)->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['commodity_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_price_reports');
    }
};
