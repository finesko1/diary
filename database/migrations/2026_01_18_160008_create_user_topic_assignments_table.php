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
        Schema::create('user_topic_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_topic_id')->constrained('user_topics');
            $table->foreignId('assignment_id')->constrained('assignments');
            $table->timestamps();

            $table->unique(['user_topic_id', 'assignment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_topic_assignments');
    }
};
