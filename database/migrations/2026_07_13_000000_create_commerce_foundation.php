<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('reserved_quantity')->default(0)->after('stock_quantity');
            $table->index(['status', 'is_featured']);
            $table->index('stock_quantity');
            $table->unique('sku');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->index('session_id');
            $table->index(['user_id', 'updated_at']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'product_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('address_id')->constrained()->nullOnDelete();
            $table->char('currency', 3)->default('INR')->after('total_amount');
            $table->unsignedInteger('loyalty_points_redeemed')->default(0)->after('currency');
            $table->decimal('loyalty_discount_amount', 10, 2)->default(0)->after('loyalty_points_redeemed');
            $table->timestamp('payment_expires_at')->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_expires_at');
            $table->timestamp('inventory_reserved_at')->nullable()->after('paid_at');
            $table->timestamp('inventory_committed_at')->nullable()->after('inventory_reserved_at');
            $table->timestamp('coupon_redeemed_at')->nullable()->after('inventory_committed_at');
            $table->timestamp('referral_rewarded_at')->nullable()->after('coupon_redeemed_at');
            $table->timestamp('shipped_at')->nullable()->after('referral_rewarded_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            $table->index(['payment_status', 'created_at']);
            $table->index(['order_status', 'created_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('bundle_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
        });

        Schema::create('cart_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();
            $table->unique(['cart_id', 'bundle_id']);
        });

        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('status', 20)->default('reserved');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('committed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->unique(['order_id', 'product_id']);
            $table->index(['status', 'expires_at']);
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('discount_amount', 10, 2);
            $table->string('status', 20)->default('reserved');
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->unique(['coupon_id', 'order_id']);
            $table->index(['coupon_id', 'user_id', 'status']);
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 30);
            $table->integer('points');
            $table->unsignedInteger('balance_after');
            $table->string('reference')->nullable()->unique();
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('stripe_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_event_id')->unique();
            $table->string('type');
            $table->string('status', 20)->default('processing');
            $table->json('payload');
            $table->text('error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->string('source', 30)->default('system');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'created_at']);
        });

        Schema::create('serviceable_pincodes', function (Blueprint $table) {
            $table->id();
            $table->string('pincode', 6)->unique();
            $table->string('city')->nullable();
            $table->string('state')->default('Uttarakhand');
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->unsignedTinyInteger('estimated_days_min')->default(2);
            $table->unsignedTinyInteger('estimated_days_max')->default(7);
            $table->boolean('cod_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'state']);
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->string('subject');
            $table->text('message');
            $table->string('status', 20)->default('new');
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->nullableMorphs('subject');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('serviceable_pincodes');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('stripe_webhook_events');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('inventory_reservations');
        Schema::dropIfExists('cart_bundle_items');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bundle_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropIndex(['payment_status', 'created_at']);
            $table->dropIndex(['order_status', 'created_at']);
            $table->dropColumn([
                'currency', 'loyalty_points_redeemed', 'loyalty_discount_amount', 'payment_expires_at', 'paid_at', 'inventory_reserved_at',
                'inventory_committed_at', 'coupon_redeemed_at', 'referral_rewarded_at',
                'shipped_at', 'delivered_at', 'cancelled_at',
            ]);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['cart_id', 'product_id']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropIndex(['user_id', 'updated_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropIndex(['status', 'is_featured']);
            $table->dropIndex(['stock_quantity']);
            $table->dropColumn('reserved_quantity');
        });
    }
};
