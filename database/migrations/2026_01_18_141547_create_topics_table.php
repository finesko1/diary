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
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('user_id')->constrained('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['subject_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
