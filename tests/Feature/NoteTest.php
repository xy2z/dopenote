<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Note;
use App\Notebook;

class NoteTest extends TestCase {

	use RefreshDatabase;

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */

	 /** @test */
	 public function an_authorized_user_can_create_a_note() {
		$notebook = factory(Notebook::class)->create();
		$note = factory(Note::class)->create();

        $this->actingAs($notebook->user)
    		->post('/note/create', $note->toArray())
    		->assertStatus(200);

		$this->assertDatabaseHas('notes', $note->only('id'));
		$this->get('/note/create', $note->toArray())
    		->assertSee($note['id'])
    		->assertSee($note['user_id'])
    		->assertSee($note['title'])
    		->assertSee($note['content'])
    		->assertSee($note['notebook_id']);
	 }

	 /** @test */
	public function an_unauthorized_user_cannot_access_a_note() {
		$note = factory(Note::class)->create();
		$this->post('/note/create', $note->toArray())
		->assertRedirect('/login');
	}

	/** @test */
	public function perm_delete_note() {
		$note = factory(Note::class)->create();
		$response = $this->call('post', $note->path().'/perm_delete');
		$this->assertEquals(302, $response->getStatusCode());
	}

	/** @test */
	public function delete_note() {
		$this->withoutMiddleware();
		$note = factory(Note::class)->create();
		$response = $this->call('POST', $note->path().'/delete');
		$this->assertEquals(200, $response->getStatusCode());
	}

	/** @test */
	public function set_content_note() {
		$note = factory(Note::class)->create();
		$response = $this->call('post', $note->path().'/set_content');
		$this->assertEquals(302, $response->getStatusCode());
	}

	/** @test */
	public function set_notebook_note() {
		$note = factory(Note::class)->create();
		$response = $this->call('post', $note->path().'/set_notebook');
		$this->assertEquals(302, $response->getStatusCode());
	}

	/** @test */
	public function set_title_note() {
		$note = factory(Note::class)->create();
		$response = $this->call('post', $note->path().'/set_title');
		$this->assertEquals(302, $response->getStatusCode());
	}

	/** @test */
	public function toggle_star_note() {
		$note = factory(Note::class)->create();
		$response = $this->call('post', $note->path().'/toggle_star');
		$this->assertEquals(302, $response->getStatusCode());
	}

	/** @test */
	public function restore_note() {
		$note = factory(Note::class)->create();
		$response = $this->call('post', $note->path().'/restore');
		$this->assertEquals(302, $response->getStatusCode());
	}
}
