<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Notebook;
use App\User;
use Faker\Generator as Faker;

$factory->define(Notebook::class, function (Faker $faker) {
	return [
		'title' => $faker->sentence(4),
		'sort_order' => $faker->randomDigit,
		'user_id' => function(){
			return factory(User::class)->create()->id;
		}
	];
});
