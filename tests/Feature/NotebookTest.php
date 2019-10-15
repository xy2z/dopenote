<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notebook;
use App\User;

class NotebookTest extends TestCase {

	use RefreshDatabase, WithFaker;

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */

	 /** @test */
	public function an_authorized_user_can_create_a_notebook() {
		$notebook = factory(Notebook::class)->create();
		$this->actingAs($notebook->user)
    		->post('/notebook/create', $notebook->toArray())
    		->assertStatus(200);

		$this->get('/notebook/create', $notebook->toArray())// can view
    		->assertSee($notebook['title'])
    		->assertSee($notebook['sort_order'])
    		->assertSee($notebook['user_id']);
	}

	/** @test */
	public function a_notebook_attributes_can_be_persisted_to_db() {
		$notebook = factory(Notebook::class)->create();
		$this->actingAs($notebook->user)
		->post($notebook->path(), $attributes = [
			'title'=>$notebook->title,
			'sort_order' => $notebook->sort_order,
			'user_id' => $notebook->user_id
		]);
		$this->assertDatabaseHas('notebooks', $attributes);
	}

	/** @test */
	public function an_unathorized_user_cannot_create_a_notebook() {
		$notebook = factory(Notebook::class)->create();
		$this->post('/notebook/create', $notebook->toArray())
		->assertRedirect('/login');
	}

	/** @test */
	public function delete_notebook() {
		$notebook = factory(Notebook::class)->create();
		$response = $this->call('post', $notebook->path().'/delete');
		$this->assertEquals(302, $response->getStatusCode());
	}

	/** @test */
	public function rename_notebook() {
		$notebook = factory(Notebook::class)->create();
		$response = $this->call('post', $notebook->path().'/rename');
		$this->assertEquals(302, $response->getStatusCode());
	}
}
