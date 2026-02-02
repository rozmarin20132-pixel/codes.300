<?php

namespace App\Services;

use App\Events\BookCreated;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class BookService
{
    /**
     * @throws \Throwable
     */
    public function createBook(array $data): Book
    {
        return DB::transaction(function () use ($data) {
            $book = Book::create($data);

            if (!empty($data['author_ids'])) {
                $book->authors()->sync($data['author_ids']);
            }

            BookCreated::dispatch($book);

            return $book->load('authors');
        });
    }

    /**
     * @throws \Throwable
     */
    public function updateBook(Book $book, array $data): Book
    {
        return DB::transaction(function () use ($book, $data) {
            $book->update($data);

            if (isset($data['author_ids'])) {
                $book->authors()->sync($data['author_ids']);
            }

            return $book->refresh()->load('authors');
        });
    }
}
