<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('session_id', 100)->nullable();
            
            // Tipe pesanan:
            $table->string('type', 20)->nullable();
            $table->integer('table_number')->nullable();
            
            // Status: nullable, diatur di controller (active, processing, completed)
            $table->string('status', 20)->nullable()->index();
            
            // Financial
            $table->decimal('subtotal', 15, 2)->nullable()->default(0);
            $table->decimal('discount_amount', 15, 2)->nullable()->default(0);
            $table->string('discount_type', 20)->nullable(); // 'rp' atau 'percent'
            $table->decimal('discount_value', 15, 2)->nullable()->default(0);
            $table->decimal('total', 15, 2)->nullable()->default(0);
            $table->text('notes')->nullable();
            
            // Metadata
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Index
            $table->index(['company_id', 'status']);
            $table->index('user_id');
            $table->index('session_id');
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            
            // Denormalisasi data (disimpan langsung agar tidak berubah jika menu diubah)
            $table->string('name', 255)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('qty')->unsigned()->nullable()->default(1);
            
            // Subtotal sebagai generated column (stored) untuk performa dan konsistensi
            $table->decimal('subtotal', 15, 2)->storedAs('price * qty')->nullable();
            
            $table->timestamps();
            
            $table->index('cart_id');
            $table->index('menu_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};