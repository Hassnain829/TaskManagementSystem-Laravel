<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    //Creating categories table from migration to database
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });

    //Creating tasks table from migration to database
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
        $table->date('due_date')->nullable();
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->softDeletes(); // ye deleted_at column add karega soft delete ke liye
        $table->timestamps();
    });

}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
