<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
             $table->string('password')->nullable(true)->change();
             $table->string('social_account_id')->nullable()->after('id');
             $table->string('social_account_type')->nullable()->after('social_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('users', function (Blueprint $table) {
             $table->string('password')->nullable(false)->change();
             $table->dropColumn('social_account_id');
             $table->dropColumn('social_account_type');
        });
    }
}
