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
        Schema::create('cash_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->bigInteger('amount')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->date('transaction_date')->useCurrent()->nullable();
            $table->timestamps();
            $table->index(['company_id', 'type', 'transaction_date']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_summaries');
    }
};
