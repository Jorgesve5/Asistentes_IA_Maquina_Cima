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
        Schema::table('alerts', function (Blueprint $table) {
            $table->index('machine_id');
        });

        Schema::table('supervisor_messages', function (Blueprint $table) {
            $table->index('machine_id');
        });

        Schema::table('manuals', function (Blueprint $table) {
            $table->index('machine_id');
        });

        Schema::table('machine_errors', function (Blueprint $table) {
            $table->index('machine_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropIndex(['machine_id']);
        });

        Schema::table('supervisor_messages', function (Blueprint $table) {
            $table->dropIndex(['machine_id']);
        });

        Schema::table('manuals', function (Blueprint $table) {
            $table->dropIndex(['machine_id']);
        });

        Schema::table('machine_errors', function (Blueprint $table) {
            $table->dropIndex(['machine_id']);
        });
    }
};
