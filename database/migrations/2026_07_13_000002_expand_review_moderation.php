<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('verified_purchase')->default(false)->after('body');
            $table->string('moderation_status', 20)->default('pending')->after('is_approved');
            $table->foreignId('moderated_by')->nullable()->after('moderation_status')->constrained('users')->nullOnDelete();
            $table->text('moderation_note')->nullable()->after('moderated_by');
            $table->timestamp('moderated_at')->nullable()->after('moderation_note');
            $table->index(['moderation_status', 'created_at']);
        });
        DB::table('reviews')->where('is_approved', true)->update(['moderation_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['moderation_status', 'created_at']);
            $table->dropConstrainedForeignId('moderated_by');
            $table->dropColumn(['verified_purchase', 'moderation_status', 'moderation_note', 'moderated_at']);
        });
    }
};
