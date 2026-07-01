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
        Schema::create('report_periods', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->nullable()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('shift_id')
                ->constrained()
                ->nullablle()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->date('date')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['company_id', 'date', 'shift_id']);
        });

        Schema::create('inventory_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_period_id')
                ->constrained()
                ->nullable()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('location')->nullable();
            $table->string('reported_by')->nullable();
            $table->date('report_date')->nullable();
            $table->dateTime('opened_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->string('cashier_name')->nullable();
            $table->decimal('total_products_sold', 15, 2)->nullable()->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();

            $table->index('report_period_id');
            $table->index('report_date');
        });

        Schema::create('inventory_report_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_report_id')
                ->constrained()
                ->nullable()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->nullable()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->decimal('first_stock', 15, 2)->nullable()->default(0);
            $table->decimal('stock_in', 15, 2)->nullable()->default(0);
            $table->decimal('selling', 15, 2)->nullable()->default(0);
            $table->decimal('remain', 15, 2)->nullable()->default(0);
            $table->timestamps();

            $table->index('inventory_report_id');
            $table->index('product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_periods');
        Schema::dropIfExists('inventory_reports');
        Schema::dropIfExists('inventory_report_items');
    }
};
