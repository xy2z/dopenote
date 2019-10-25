<?php

namespace App\Providers;

use App\Note;
use App\Notebook;
use App\Policies\NotePolicy;
use App\Policies\NotebookPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		'App\Model' => 'App\Policies\ModelPolicy',
		Note::class => NotePolicy::class,
		Notebook::class => NotebookPolicy::class,
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->registerPolicies();

		//
	}
}
