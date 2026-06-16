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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name')->nullable();
            $table->timestamps();
            $table->index('id');
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('module')->nullable();
            $table->string('page_name')->nullable();
            $table->string('action')->nullable();
            $table->timestamps();
            $table->index('id');
        });

        Schema::create('group_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->nullable()->constrained('groups')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('pages')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('access')->nullable();
            $table->timestamps();
            $table->index('group_id');
            $table->index('page_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('group_pages');
    }
};
