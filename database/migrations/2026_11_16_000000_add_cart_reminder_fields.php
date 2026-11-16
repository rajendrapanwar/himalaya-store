<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // When the basket reminder email was last sent for this cart.
        Schema::table('carts', function (Blueprint $table) {
            $table->timestamp('reminded_at')->nullable()->after('user_id');
        });

        // Set when a customer clicks "stop these emails" in a basket reminder.
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('cart_reminders_unsubscribed_at')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('reminded_at');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cart_reminders_unsubscribed_at');
        });
    }
};
