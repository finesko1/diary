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
        Schema::create('user_education_data', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->constrained('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->date('beginning_of_teaching')->nullable();
            $table->integer('course')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_education_data');
    }
};
