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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_type_id')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'graded', 'returned'])->default('pending');
            $table->integer('mark')->nullable();
            $table->timestamps();

            $table->foreign('assignment_type_id')
                ->references('id')
                ->on('assignment_types')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
