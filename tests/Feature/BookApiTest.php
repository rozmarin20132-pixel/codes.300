<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Events\BookCreated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_authenticated_user_can_create_a_book_with_authors(): void
    {
        Event::fake();
        $user = User::factory()->create();
        $authors = Author::factory()->count(2)->create();

        $payload = [
            'title' => 'Wiedźmin: Ostatnie życzenie',
            'author_ids' => $authors->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', $payload['title']);

        $this->assertDatabaseHas('books', ['title' => 'Wiedźmin: Ostatnie życzenie']);
        $this->assertDatabaseCount('author_book', 2);

        Event::assertDispatched(BookCreated::class);
    }

    #[Test]
    public function test_unauthenticated_user_cannot_create_a_book(): void
    {
        $payload = [
            'title' => 'Unauthorized Book',
            'author_ids' => [1],
        ];

        $response = $this->postJson('/api/books', $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function test_it_fails_to_create_a_book_without_a_title(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', [
                'author_ids' => [1]
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function test_it_fails_to_create_a_book_with_non_existent_author(): void
    {
        $user = User::factory()->create();
        $nonExistentId = 999;

        $payload = [
            'title' => 'Ghost Book',
            'author_ids' => [$nonExistentId],
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['author_ids.0']);
    }

    #[Test]
    public function it_can_delete_a_book(): void
    {
        $book = Book::factory()->create();
        $author = Author::factory()->create();
        $book->authors()->attach($author);

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);

        $this->assertDatabaseMissing('author_book', [
            'book_id' => $book->id
        ]);
    }

    #[Test]
    public function test_any_user_can_update_a_book(): void
    {
        $book = Book::factory()->create(['title' => 'Старое название']);
        $payload = ['title' => 'Новое название'];

        $response = $this->putJson("/api/books/{$book->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Новое название'
        ]);
    }

    #[Test]
    public function test_any_user_can_list_books(): void
    {
        Book::factory()->count(5)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    #[Test]
    public function test_any_user_can_get_single_book_by_id(): void
    {
        $author = Author::factory()->create(['name' => 'J.R.R. Tolkien']);
        $book = Book::factory()->create(['title' => 'The Fellowship of the Ring']);
        $book->authors()->attach($author);

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $book->id)
            ->assertJsonPath('data.title', 'The Fellowship of the Ring')
            ->assertJsonStructure([
                'data' => [
                    'id', 'title', 'authors'
                ]
            ])
            ->assertJsonPath('data.authors.0.name', 'J.R.R. Tolkien');
    }

    #[Test]
    public function test_it_returns_404_if_book_does_not_exist(): void
    {
        $nonExistentId = 999;

        $response = $this->getJson("/api/books/{$nonExistentId}");

        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }
}
