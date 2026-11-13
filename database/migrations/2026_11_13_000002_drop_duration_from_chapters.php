<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Длительность главы больше не используется: у главы есть страницы,
     * а время чтения считается по тексту.
     *
     * В старых установках колонка ещё существует и удаляется здесь.
     * В новых её уже нет в исходной миграции chapters.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('chapters', 'duration')) {
            return;
        }

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('chapters', 'duration')) {
            return;
        }

        Schema::table('chapters', function (Blueprint $table) {
            $table->string('duration')->nullable()->after('number');
        });
    }
};
