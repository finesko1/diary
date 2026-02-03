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
        Schema::create('subject_levels', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->constrained('users');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->string('level');
            $table->uuid('evaluated_by')->constrained('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->text('certificate_info')->nullable();
            $table->date('certificate_date')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_levels');
    }
};
