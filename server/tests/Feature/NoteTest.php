<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_fetches_paginated_notes_from_index()
    {

        Note::factory()->count(15)->create();

        $response = $this->getJson('/api/notes?search=');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ])
            ->assertJsonCount(10, 'data');
    }

    /** @test */
    public function it_creates_a_new_note()
    {
        $data = [
            'title' => 'Test Note',
            'description' => 'This is a test note.',
        ];

        $response = $this->postJson('/api/notes', $data);

        $response->assertStatus(201)
            ->assertJson($data);
    }

    /** @test */
    public function it_shows_a_specific_note()
    {
        $note = Note::factory()->create();

        $response = $this->getJson("/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJson($note->toArray());
    }

    /** @test */
    public function it_updates_an_existing_note()
    {
        $note = Note::factory()->create();

        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated description.',
        ];

        $response = $this->putJson("/api/notes/{$note->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson($updatedData);
    }

    /** @test */
    public function it_deletes_an_existing_note()
    {
        $note = Note::factory()->create();

        $response = $this->deleteJson("/api/notes/{$note->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('notes', [
            'id' => $note->id,
        ]);
    }
}
