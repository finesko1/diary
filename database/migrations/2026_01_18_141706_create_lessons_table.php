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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('teacher_id')->constrained('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('student_id')->constrained('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('date');
            $table->timestamps();

            $table->index(['student_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_topics');
    }
};
