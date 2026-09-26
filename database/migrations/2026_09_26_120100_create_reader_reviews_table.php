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
        Schema::create('reader_reviews', function (Blueprint $table) {
            $table->id();
            $table->text('quote');
            $table->string('author');
            $table->string('context')->nullable()->comment('e.g. "Amazon review", "sponsor"');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false)->comment('Shown in the home page carousel');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Reader reviews used to live as a JSON list on the book; carry any over.
        $now = now();

        DB::table('books')->whereNotNull('testimonials')->orderBy('id')->each(function (object $book) use ($now) {
            foreach (json_decode($book->testimonials, true) ?: [] as $i => $testimonial) {
                DB::table('reader_reviews')->insert([
                    'quote' => $testimonial['quote'],
                    'author' => $testimonial['author'],
                    'context' => $testimonial['context'] ?? null,
                    'sort_order' => $i,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('testimonials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->json('testimonials')->nullable()->after('excerpts');
        });

        Schema::dropIfExists('reader_reviews');
    }
};
