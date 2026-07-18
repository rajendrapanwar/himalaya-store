<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow', 100)->nullable();
            $table->string('title', 180);
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->string('image_alt', 180);
            $table->string('primary_label', 60)->nullable();
            $table->string('primary_url', 500)->nullable();
            $table->string('secondary_label', 60)->nullable();
            $table->string('secondary_url', 500)->nullable();
            $table->string('text_theme', 10)->default('light');
            $table->string('text_position', 10)->default('left');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        $now = now();
        DB::table('hero_slides')->insert([
            [
                'eyebrow' => 'From Uttarakhand, with care',
                'title' => 'Good food has a place, a maker and a story.',
                'description' => 'Traceable honey, teas, pulses, grains, spices and craft from small mountain producers—packed thoughtfully in Dehradun.',
                'image_path' => 'assets/img/hero/himalayan-collection-v2.webp',
                'image_alt' => 'Himalayan honey, herbal tea, pulses, grains, spices and handmade craft',
                'primary_label' => 'Shop the collection',
                'primary_url' => '/products',
                'secondary_label' => 'Our sourcing story',
                'secondary_url' => '/about',
                'text_theme' => 'light',
                'text_position' => 'left',
                'sort_order' => 10,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'eyebrow' => 'Share the hills, earn together',
                'title' => 'Invite a friend. Both of you earn rewards.',
                'description' => 'Share your personal referral link. After your friend’s first qualifying order, you receive 100 points and they receive 50 points.',
                'image_path' => 'assets/img/products/wild-forest-honey.webp',
                'image_alt' => 'A jar of Himalayan forest honey ready to be shared',
                'primary_label' => 'See how rewards work',
                'primary_url' => '/rewards-and-referrals',
                'secondary_label' => 'Open my account',
                'secondary_url' => '/account',
                'text_theme' => 'light',
                'text_position' => 'left',
                'sort_order' => 20,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
