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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('facebook_post_id')->nullable()->after('is_featured')
                ->comment('Set once the post has been shared to the Facebook Page');
            $table->string('instagram_media_id')->nullable()->after('facebook_post_id')
                ->comment('Set once the post has been shared to Instagram');
            $table->text('social_share_error')->nullable()->after('instagram_media_id')
                ->comment('Why the last automatic share failed, shown in the editor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['facebook_post_id', 'instagram_media_id', 'social_share_error']);
        });
    }
};
