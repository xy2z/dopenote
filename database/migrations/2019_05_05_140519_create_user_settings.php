<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSettings extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('user_settings', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedInteger('user_id')->unique();

			$table->unsignedInteger('font_size');
			$table->string('font_family');
			$table->float('line_height')->unsigned(true);

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('user_settings');
	}
}
