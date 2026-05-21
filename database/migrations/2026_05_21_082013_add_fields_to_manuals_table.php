<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manuals', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            $table->string('category')->default('Manual de Operación');
            $table->string('file_type')->default('pdf');
            $table->boolean('in_chat')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manuals', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'category', 'file_type', 'in_chat']);
        });
    }
};
