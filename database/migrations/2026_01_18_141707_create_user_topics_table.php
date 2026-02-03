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
//            $table->foreignId('subject_id')->constrained('subjects')
//                ->onUpdate('cascade')->onDelete('cascade');
//            $table->uuid('teacher_id')->constrained('users')
//                ->onUpdate('cascade')->onDelete('cascade');
//            $table->uuid('student_id')->constrained('users')
//                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lessons')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('topic_id')->nullable();
            $table->integer('mark')->nullable();
//            $table->dateTime('date');
            $table->timestamps();

            $table->foreign('topic_id')
                ->references('id')
                ->on('topics')
                ->onUpdate('cascade')
                ->onDelete('set null');
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
