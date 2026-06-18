<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('current_stock', 15, 2)->nullable()->default(0);
            $table->timestamps();
            $table->index('product_variant_id');
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('pic_id')->nullable()->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('movement_type', 50)->nullable();
            $table->decimal('qty', 15, 2)->nullable();
            $table->decimal('stock_before', 15, 2)->nullable();
            $table->decimal('stock_after', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('product_variant_id');
            $table->index('pic_id');
        });

        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->decimal('system_stock', 15, 2)->nullable();
            $table->decimal('physical_stock', 15, 2)->nullable();
            $table->decimal('difference', 15, 2)->nullable();
            $table->text('status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_opnames');
    }
};
