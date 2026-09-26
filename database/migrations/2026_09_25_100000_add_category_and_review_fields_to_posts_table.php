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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('category')->default('reflection')->after('author_id')->index();
            $table->string('cover_image_path')->nullable()->after('excerpt');
            $table->string('reviewed_book_title')->nullable()->after('body');
            $table->string('reviewed_book_author')->nullable()->after('reviewed_book_title');
            $table->unsignedTinyInteger('rating')->nullable()->after('reviewed_book_author');
            $table->json('affiliate_links')->nullable()->after('rating');
        });

        // Bodies were plain text until now; the editor stores HTML.
        DB::table('posts')->orderBy('id')->each(function (object $post) {
            if (preg_match('/^\s*<(p|h[1-6]|ul|ol|div|blockquote|figure|img)\b/i', $post->body)) {
                return;
            }

            $paragraphs = preg_split('/\n\s*\n/', trim($post->body)) ?: [];

            $html = collect($paragraphs)
                ->map(fn (string $p) => '<p>'.nl2br(e(trim($p)), false).'</p>')
                ->implode('');

            DB::table('posts')->where('id', $post->id)->update(['body' => $html]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn([
                'category',
                'cover_image_path',
                'reviewed_book_title',
                'reviewed_book_author',
                'rating',
                'affiliate_links',
            ]);
        });
    }
};
