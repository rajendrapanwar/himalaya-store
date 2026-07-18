<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', fn (Blueprint $table) => $table->index(['status', 'name'], 'categories_status_name_index'));
        Schema::table('products', fn (Blueprint $table) => $table->index(['category_id', 'status', 'created_at'], 'products_category_status_created_index'));
        Schema::table('reviews', fn (Blueprint $table) => $table->index(['product_id', 'is_approved', 'created_at'], 'reviews_product_approved_created_index'));
        Schema::table('bundles', fn (Blueprint $table) => $table->index(['is_active', 'created_at'], 'bundles_active_created_index'));
        Schema::table('coupons', fn (Blueprint $table) => $table->index(['is_active', 'starts_at', 'expires_at'], 'coupons_active_window_index'));
        Schema::table('orders', fn (Blueprint $table) => $table->index(['user_id', 'created_at'], 'orders_user_created_index'));
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropIndex('orders_user_created_index'));
        Schema::table('coupons', fn (Blueprint $table) => $table->dropIndex('coupons_active_window_index'));
        Schema::table('bundles', fn (Blueprint $table) => $table->dropIndex('bundles_active_created_index'));
        Schema::table('reviews', fn (Blueprint $table) => $table->dropIndex('reviews_product_approved_created_index'));
        Schema::table('products', fn (Blueprint $table) => $table->dropIndex('products_category_status_created_index'));
        Schema::table('categories', fn (Blueprint $table) => $table->dropIndex('categories_status_name_index'));
    }
};
