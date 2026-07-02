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
        Schema::create('cashier_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('restrict');

            $table->decimal('opening_balance', 15, 2);
            $table->decimal('closing_balance', 15, 2)->nullable();
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_cash_in', 15, 2)->default(0);
            $table->decimal('total_cash_out', 15, 2)->default(0);
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->string('status')->default('open')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('opened_at');
        });

        Schema::create('cash_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')
                ->nullable()
                ->constrained('cashier_sessions')
                ->onDelete('set null');
            $table->string('type')->nullable();
            $table->bigInteger('amount')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->date('transaction_date')->useCurrent()->nullable();
            $table->timestamps();
            $table->index(['company_id', 'type', 'transaction_date','session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_sessions');
        Schema::dropIfExists('cash_summaries');
    }
};
