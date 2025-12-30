// database/migrations/2025_12_30_add_ban_fields_to_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBanFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('banned_at')->nullable()->after('email_verified_at');
            $table->foreignId('banned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('ban_reason')->nullable();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['ban_reason', 'banned_by', 'banned_at']);
        });
    }
}