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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('variant_name')->nullable();
            $table->string('variant_code')->nullable();
            $table->string('image')->nullable();
            $table->decimal('purchase_price', 15, 2)->default(0)->nullable();
            $table->decimal('selling_price', 15, 2)->default(0)->nullable();
            $table->boolean('is_active')->default(true)->nullable();

            $table->timestamps();

            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
