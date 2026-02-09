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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('filename')->nullable();
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('extension')->nullable();
            $table->enum('type', ['image', 'video', 'audio', 'file', 'archive', 'other']);

            // Метаданные в зависимости от типа
            $table->json('metadata')->nullable();

            // Для изображений
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();

            // Для видео/аудио
            $table->integer('duration')->nullable();
            $table->string('codec')->nullable();
            $table->integer('bitrate')->nullable();

            // Связи
            $table->uuid('user_id')->nullable()->constrained()
                ->onDelete('cascade');
            $table->morphs('attachable'); // Полиморфная связь

            // Индексы
            $table->index(['type', 'user_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
