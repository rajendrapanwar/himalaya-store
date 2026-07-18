<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $t) {
            $t->string('producer_name')->nullable();
            $t->string('producer_location')->nullable();
            $t->string('batch_number')->nullable();
            $t->date('harvested_at')->nullable();
            $t->text('ingredients')->nullable();
        });
        Schema::table('users', function (Blueprint $t) {
            $t->unsignedInteger('loyalty_points')->default(0);
            $t->string('referral_code')->nullable()->unique();
            $t->foreignId('referred_by_id')->nullable()->constrained('users')->nullOnDelete();
        });
        Schema::table('orders', function (Blueprint $t) {
            $t->string('tracking_number')->nullable();
            $t->string('courier_name')->nullable();
            $t->string('coupon_code')->nullable();
            $t->decimal('discount_amount', 10, 2)->default(0);
        });
        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('rating');
            $t->string('title')->nullable();
            $t->text('body');
            $t->boolean('is_approved')->default(false);
            $t->timestamps();
            $t->unique(['product_id', 'user_id']);
        });
        Schema::create('wishlists', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['user_id', 'product_id']);
        });
        Schema::create('coupons', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->enum('type', ['fixed', 'percent']);
            $t->decimal('value', 10, 2);
            $t->decimal('minimum_amount', 10, 2)->default(0);
            $t->unsignedInteger('usage_limit')->nullable();
            $t->unsignedInteger('used_count')->default(0);
            $t->timestamp('starts_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('bundles', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->decimal('price', 10, 2);
            $t->string('image')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('bundle_product', function (Blueprint $t) {
            $t->id();
            $t->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('quantity')->default(1);
            $t->unique(['bundle_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_product');
        Schema::dropIfExists('bundles');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('reviews');
        Schema::table('orders', fn (Blueprint $t) => $t->dropColumn(['tracking_number', 'courier_name', 'coupon_code', 'discount_amount']));
        Schema::table('users', fn (Blueprint $t) => $t->dropConstrainedForeignId('referred_by_id')->dropColumn(['loyalty_points', 'referral_code']));
        Schema::table('products', fn (Blueprint $t) => $t->dropColumn(['producer_name', 'producer_location', 'batch_number', 'harvested_at', 'ingredients']));
    }
};
