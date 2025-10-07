<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 既存チェックを入れて冪等に
        if (!Schema::hasColumn('users', 'bio') || !Schema::hasColumn('users', 'avatar_path')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'bio')) {
                    $table->text('bio')->nullable()->after('name');
                }
                if (!Schema::hasColumn('users', 'avatar_path')) {
                    // bio のあとに置きたいだけなので、after('bio') に固定でOK
                    $table->string('avatar_path')->nullable()->after('bio');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'avatar_path')) {
                $table->dropColumn('avatar_path');
            }
            if (Schema::hasColumn('users', 'bio')) {
                $table->dropColumn('bio');
            }
        });
    }
};

