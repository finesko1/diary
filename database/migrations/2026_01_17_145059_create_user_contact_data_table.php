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
        Schema::create('user_contact_data', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->constrained('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('city')->nullable();
            $table->string('telephone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('telegram')->nullable();
            $table->string('vk')->nullable();
            $table->string('calls_platform')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_contact_data');
    }
};
