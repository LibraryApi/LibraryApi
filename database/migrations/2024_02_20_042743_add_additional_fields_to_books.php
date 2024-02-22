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
        Schema::table('books', function (Blueprint $table) {
            $table->string('cover_image')->nullable();
            $table->string('author_bio')->nullable();
            $table->string('language')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('number_of_pages')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('comments_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('views_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('cover_image');
            $table->dropColumn('author_bio');
            $table->dropColumn('language');
            $table->dropColumn('rating');
            $table->dropColumn('number_of_pages');
            $table->dropColumn('is_published');
            $table->dropColumn('comments_count');
            $table->dropColumn('likes_count');
            $table->dropColumn('views_count');
        });
    }
};
