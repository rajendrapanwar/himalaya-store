<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0)->change();
        });
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->integer('balance_after')->change();
        });
        Schema::table('coupons', function (Blueprint $table) {
            $table->decimal('max_discount_amount', 10, 2)->nullable()->after('value');
            $table->unsignedInteger('per_user_limit')->default(1)->after('usage_limit');
            $table->boolean('first_order_only')->default(false)->after('per_user_limit');
            $table->boolean('free_shipping')->default(false)->after('first_order_only');
        });
        Schema::create('category_coupon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->unique(['coupon_id', 'category_id']);
        });
        Schema::create('coupon_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unique(['coupon_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_product');
        Schema::dropIfExists('category_coupon');
        Schema::table('coupons', fn (Blueprint $table) => $table->dropColumn(['max_discount_amount', 'per_user_limit', 'first_order_only', 'free_shipping']));
        Schema::table('users', fn (Blueprint $table) => $table->unsignedInteger('loyalty_points')->default(0)->change());
        Schema::table('loyalty_transactions', fn (Blueprint $table) => $table->unsignedInteger('balance_after')->change());
    }
};
