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
        Schema::create('machines', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('status')->default('online'); // online, warning, maintenance, waiting
            $table->string('serial')->nullable();
            $table->string('indicator')->nullable();
            $table->integer('column');
            $table->integer('row');
            $table->string('subLabel')->nullable();
            $table->timestamps();
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('machine_id');
            $table->string('machine_name');
            $table->text('message');
            $table->string('type'); // warning, maintenance, waiting, info
            $table->string('timestamp');
            $table->boolean('read')->default(false);
            $table->timestamps();
        });

        Schema::create('supervisor_messages', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('machine_id');
            $table->string('machine_name');
            $table->text('text');
            $table->string('from'); // operator, admin
            $table->string('senderName');
            $table->string('timestamp');
            $table->boolean('read')->default(false);
            $table->timestamps();
        });

        Schema::create('manuals', function (Blueprint $table) {
            $table->id();
            $table->string('machine_id');
            $table->string('fileName');
            $table->string('size');
            $table->longText('text'); // parsed RAG chunks concatenated or stored as JSON chunks
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuals');
        Schema::dropIfExists('supervisor_messages');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('machines');
    }
};
