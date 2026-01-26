<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')
                ->references('id')->on('users')->onDelete('cascade');
            $table->uuid('friend_id')
                ->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', [
                'pending',
                'accepted',
                'blocked',
                'declined',
            ])->default('pending');
            // Кто инициировал запрос (для pending/accepted/declined)
            $table->uuid('initiator_id')
                ->references('id')->on('users')->onDelete('cascade')->nullable();
            // Тип блокировки (если status = 'blocked')
            $table->enum('block_type', [
                'user_blocked_friend',   // Пользователь заблокировал друга
                'friend_blocked_user',   // Друг заблокировал пользователя
                'mutual_block',          // Взаимная блокировка
            ])->nullable();
            // Дата изменения статуса
            $table->timestamps();

            // Уникальная пара пользователей
            $table->unique(['user_id', 'friend_id']);

            // Индексация таблицы
            $table->index(['friend_id', 'status']);
            $table->index(['initiator_id', 'status']);
        });

        // Триггер для автоматического упорядочивания ID
        DB::statement('
            CREATE OR REPLACE FUNCTION order_friendship_ids()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.user_id > NEW.friend_id THEN
                    NEW := ROW(
                        NEW.id,
                        NEW.friend_id,
                        NEW.user_id,
                        NEW.status,
                        NEW.initiator_id,
                        NEW.block_type,
                        NEW.created_at,
                        NEW.updated_at
                    );
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        DB::statement('
            CREATE TRIGGER order_friendship_ids_trigger
            BEFORE INSERT OR UPDATE ON friendships
            FOR EACH ROW
            EXECUTE FUNCTION order_friendship_ids();
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS order_friendship_ids');
        DB::statement('DROP TRIGGER IF EXISTS order_friendship_ids_update');

        Schema::dropIfExists('friendships');
    }
};
