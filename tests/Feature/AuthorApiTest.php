<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthorApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_it_returns_paginated_authors_list(): void
    {
        Author::factory()->count(20)->create();

        $response = $this->getJson('/api/authors');

        $response->assertStatus(200)
            ->assertJsonCount(15, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'last_book_title', 'books']
                ],
                'links',
                'meta'
            ]);
    }

    #[Test]
    public function test_it_filters_authors_by_book_title(): void
    {
        $author1 = Author::factory()->create(['name' => 'Tolkien']);
        $book1 = Book::factory()->create(['title' => 'The Hobbit']);
        $author1->books()->attach($book1);

        $author2 = Author::factory()->create(['name' => 'Sapkowski']);
        $book2 = Book::factory()->create(['title' => 'The Witcher']);
        $author2->books()->attach($book2);

        $response = $this->getJson('/api/authors?search=Hobbit');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Tolkien');

        $response->assertJsonMissing(['name' => 'Sapkowski']);
    }

    #[Test]
    public function test_it_returns_single_author_details(): void
    {
        $author = Author::factory()->create();
        $books = Book::factory()->count(2)->create();
        $author->books()->attach($books);

        $response = $this->getJson("/api/authors/{$author->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $author->id)
            ->assertJsonCount(2, 'data.books');
    }

    #[Test]
    public function test_it_returns_empty_data_if_no_matches_found(): void
    {
        Author::factory()->create();
        Book::factory()->create(['title' => 'Existing Book']);

        $response = $this->getJson('/api/authors?search=NonExistentTitle');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
