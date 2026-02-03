<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Seeder;

class AuthorBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = Author::factory()->count(10)->create();

        $books = Book::factory()->count(30)->create();

        $authors->each(function (Author $author) use ($books) {
            $author->books()->attach($books->random()->id);
        });

        Book::doesntHave('authors')->each(function (Book $book) use ($authors) {
            $extraAuthors = $authors
                ->random(rand(1, 2))
                ->pluck('id');

            $book->authors()->syncWithoutDetaching($extraAuthors);
        });

        $authors->each(function (Author $author) {
            $lastBook = $author->books()->latest('id')->first();
            $author->update([
                'last_book_title' => $lastBook->title,
            ]);
        });
    }
}
