<?php
namespace App\Listeners;

use App\Events\BookCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateAuthorStats implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BookCreated $event): void
    {
        $book = $event->book;

        $book->loadMissing('authors');

        foreach ($book->authors as $author) {
            $lastBook = $author->books()->latest('id')->first();

            if ($lastBook) {
                $author->update([
                    'last_book_title' => $lastBook->title
                ]);
            }
        }
    }
}
