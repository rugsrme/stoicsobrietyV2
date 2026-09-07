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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('sample_path')->nullable();

            $table->string('author_name')->nullable();
            $table->text('author_bio')->nullable();
            $table->string('author_photo_path')->nullable();

            $table->json('excerpts')->nullable();
            $table->json('retailer_links')->nullable();

            $table->unsignedInteger('price')->nullable()->comment('Price in cents');
            $table->string('currency', 3)->default('USD');
            $table->string('purchase_type')->default('link')->comment('link|stripe');
            $table->string('stripe_price_id')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
