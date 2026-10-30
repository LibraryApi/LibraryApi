<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('avatar')->nullable()->after('email');
            });
        }

        $columns = [
            'banner_image' => fn (Blueprint $table) => $table->string('banner_image')->nullable()->after('avatar'),
            'birth_date' => fn (Blueprint $table) => $table->date('birth_date')->nullable()->after('banner_image'),
            'city' => fn (Blueprint $table) => $table->string('city', 100)->nullable()->after('birth_date'),
            'telegram' => fn (Blueprint $table) => $table->string('telegram', 100)->nullable()->after('city'),
            'phone' => fn (Blueprint $table) => $table->string('phone', 30)->nullable()->after('telegram'),
            'github' => fn (Blueprint $table) => $table->string('github', 100)->nullable()->after('phone'),
            'vk' => fn (Blueprint $table) => $table->string('vk', 100)->nullable()->after('github'),
            'habr' => fn (Blueprint $table) => $table->string('habr', 100)->nullable()->after('vk'),
            'about' => fn (Blueprint $table) => $table->text('about')->nullable()->after('habr'),
        ];

        foreach ($columns as $name => $definition) {
            if (!Schema::hasColumn('users', $name)) {
                Schema::table('users', function (Blueprint $table) use ($definition) {
                    $definition($table);
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['banner_image', 'birth_date', 'city', 'telegram', 'phone', 'github', 'vk', 'habr', 'about'] as $name) {
            if (Schema::hasColumn('users', $name)) {
                Schema::table('users', function (Blueprint $table) use ($name) {
                    $table->dropColumn($name);
                });
            }
        }
    }
};
