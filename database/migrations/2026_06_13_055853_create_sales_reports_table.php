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
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('report_date')->nullable();
            $table->date('arrived_date')->nullable();
            $table->decimal('pulsa_amount',15,2)->nullable();
            $table->decimal('accessories_amount',15,2)->nullable();
            $table->decimal('service_amount',15,2)->nullable();
            $table->decimal('total_amount',15,2)->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_reports');
    }
};
