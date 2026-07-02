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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('draft_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignId('session_id')
                ->nullable()
                ->constrained('cashier_sessions')
                ->onDelete('set null');
            $table->string('transaction_number')->unique();

            $table->timestamp('transaction_date')->nullable()->useCurrent();

            $table->bigInteger('subtotal')->nullable()->default(0);
            $table->string('discount_type')->nullable();
            $table->bigInteger('discount_value')->nullable()->default(0);
            $table->bigInteger('discount_amount')->nullable()->default(0);
            $table->bigInteger('total')->nullable()->default(0);

            $table->string('payment_method'); // 'cash' or 'qris'
            $table->bigInteger('paid')->nullable()->default(0);
            $table->bigInteger('change')->nullable()->default(0);

            // Status transaksi (biasanya 'completed')
            $table->string('status')->nullable();

            $table->timestamps();

            // Index untuk pencarian cepat
            $table->index('transaction_number');
            $table->index('transaction_date');
            $table->index('user_id');
            $table->index('session_id');
            $table->index('company_id');
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->bigInteger('price')->nullable();
            $table->integer('qty')->nullable();
            $table->bigInteger('subtotal')->nullable();
            // opsional diskon per item
            $table->bigInteger('discount_per_item')->nullable()->default(0);
            $table->timestamps();

            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('transaction_items');
    }
};
