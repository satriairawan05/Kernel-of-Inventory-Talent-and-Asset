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
            $table->timestamp('last_updated_at')->nullable()->useCurrentOnUpdate();
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
            $table->string('receiver_sender')->nullable();
            $table->timestamps();
            $table->index('product_variant_id');
            $table->index('pic_id');
        });

        Schema::create('stock_opname_periods', function (Blueprint $table) {
            $table->id();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->decimal('system_stock', 15, 2)->nullable();
            $table->decimal('physical_stock', 15, 2)->nullable();
            $table->decimal('difference', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('stock_opname_period_id');
            $table->index('product_variant_id');
            $table->index('reported_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_opname_periods');
        Schema::dropIfExists('stock_opname_details');
    }
};
