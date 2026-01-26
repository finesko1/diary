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
        Schema::create('user_topics', function (Blueprint $table) {
            $table->id();
            $table->uuid('teacher_id')->constrained('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('student_id')->constrained('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('topic_id')->constrained('topics')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->integer('mark')->nullable();
            $table->dateTime('date');
            $table->timestamps();

            $table->index(['student_id', 'date']);
            $table->unique(['student_id', 'topic_id', 'date']);
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
