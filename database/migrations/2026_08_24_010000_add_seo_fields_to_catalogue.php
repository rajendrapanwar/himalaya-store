<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('seo_title', 70)->nullable()->after('description');
            $table->string('meta_description', 170)->nullable()->after('seo_title');
            $table->string('focus_keyword', 120)->nullable()->after('meta_description');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('seo_title', 70)->nullable()->after('description');
            $table->string('meta_description', 170)->nullable()->after('seo_title');
            $table->string('focus_keyword', 120)->nullable()->after('meta_description');
        });

        Schema::table('journal_posts', function (Blueprint $table) {
            $table->string('focus_keyword', 120)->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['seo_title', 'meta_description', 'focus_keyword']));
        Schema::table('categories', fn (Blueprint $table) => $table->dropColumn(['seo_title', 'meta_description', 'focus_keyword']));
        Schema::table('journal_posts', fn (Blueprint $table) => $table->dropColumn('focus_keyword'));
    }
};
