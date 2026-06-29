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
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('type')->nullable();
            $table->unsignedInteger('table_number')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->decimal('subtotal', 15, 2)->nullable()->default(0);
            $table->timestamps();

            $table->index('company_id');
            $table->index('status');
        });

        Schema::create('draft_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('qty')->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->timestamps();

            $table->index('draft_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drafts');
        Schema::dropIfExists('draft_items');
    }
};
