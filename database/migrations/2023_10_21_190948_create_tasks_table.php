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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('priority', ['normal', 'medium', 'high', 'urgent'])->default('normal');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('card_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->timestamps();
            $table->foreign('created_by', 'task_created_by_foreign_key')->on('users')->references('id')->onDelete('cascade');
            $table->unique(['name', 'project_id'], 'card_unique_with_name_and_created_by');
//            $table->foreign('card_id', 'card_card_id_foreign_key')->on('cards')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
