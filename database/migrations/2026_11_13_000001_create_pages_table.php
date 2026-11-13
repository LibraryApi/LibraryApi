<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Страницы внутри главы. Глава — контейнер, страница — единица текста,
     * которую читатель видит за раз.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->longText('content');
            $table->unsignedInteger('number')->nullable();
            $table->timestamps();

            $table->index(['chapter_id', 'number']);
        });

        // Переносим уже написанный текст глав в первую страницу каждой главы,
        // чтобы книги не потеряли содержимое после перехода на страницы.
        DB::table('chapters')
            ->select(['id', 'content', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->chunkById(100, function ($chapters) {
                $now = now();

                $rows = $chapters->map(function ($chapter) use ($now) {
                    return [
                        'chapter_id' => $chapter->id,
                        'title' => null,
                        'content' => $chapter->content ?? '',
                        'number' => 1,
                        'created_at' => $chapter->created_at ?? $now,
                        'updated_at' => $chapter->updated_at ?? $now,
                    ];
                })->all();

                if ($rows !== []) {
                    DB::table('pages')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
