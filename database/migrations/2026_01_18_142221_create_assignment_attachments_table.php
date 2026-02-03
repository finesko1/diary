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
        Schema::create('assignment_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->uuid('user_id')->constrained(); // Кто загрузил
            $table->enum('type', ['file', 'image', 'video', 'audio', 'document', 'link', 'text']);
            $table->text('description')->nullable();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->integer('size')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['assignment_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_attachments');
    }
};
