<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reposts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tweet_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // 同じユーザーが同じツイートを2回リポストできないように
            $table->unique(['user_id', 'tweet_id']);
            // タイムライン並び替え等のための補助
            $table->index(['tweet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reposts');
    }
};
