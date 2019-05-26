<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Note;
use Faker\Generator as Faker;
use App\Notebook;

$factory->define(Note::class, function (Faker $faker) {
	return [
		'notebook_id'=> function(){
			return factory(Notebook::class)->create()->id;
		},
		'title' => $faker->sentence(4),
		'content' => $faker->paragraph,
		'user_id' => function(){
			return factory(Notebook::class)->create()->user_id;
		}
	];
});
