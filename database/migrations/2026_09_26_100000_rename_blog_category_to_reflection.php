<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The "blog" section is now called "Reflections".
     */
    public function up(): void
    {
        DB::table('posts')->where('category', 'blog')->update(['category' => 'reflection']);

        Schema::table('posts', function (Blueprint $table) {
            $table->string('category')->default('reflection')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('posts')->where('category', 'reflection')->update(['category' => 'blog']);

        Schema::table('posts', function (Blueprint $table) {
            $table->string('category')->default('blog')->change();
        });
    }
};
