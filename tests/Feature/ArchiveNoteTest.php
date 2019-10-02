<?php

namespace Tests\Feature;

use App\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArchiveNoteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authorized_users_can_archive_notes()
    {
        $note = factory(Note::class)->create();

        $this->actingAs($note->user)
            ->post("/note/{$note->id}/archive")
            ->assertStatus(200);
    }

    /** @test */
    public function archived_notes_cannot_be_edited()
    {
        $note = factory(Note::class)->create([
            'archived_at' => now(),
        ]);

        $this->actingAs($note->user)
            ->post("/note/{$note->id}/set_title", [
                'title' => 'New title',
            ])
            ->assertStatus(200);
    }
}
