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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete()->after('password');
            $table->index('company_id');
            // $table->foreignId('group_id')->nullable()->constrained('groups')->cascadeOnUpdate()->cascadeOnDelete();
            // $table->index('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete()->after('password');
            $table->index('company_id');
            // $table->foreignId('group_id')->nullable()->constrained('groups')->cascadeOnUpdate()->cascadeOnDelete();
            // $table->index('group_id');
        });
    }
};
