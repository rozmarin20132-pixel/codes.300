<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookWithAuthorResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return BookWithAuthorResource::collection(
            Book::with('authors')->paginate(10)
        );
    }

    /**
     * @throws \Throwable
     */
    public function store(StoreBookRequest $request): BookWithAuthorResource
    {
        $book = $this->bookService->createBook($request->validated());

        return new BookWithAuthorResource($book);
    }

    public function show(Book $book): BookWithAuthorResource
    {
        return new BookWithAuthorResource($book->loadMissing('authors'));
    }

    /**
     * @throws \Throwable
     */
    public function update(UpdateBookRequest $request, Book $book): BookWithAuthorResource
    {
        $updatedBook = $this->bookService->updateBook($book, $request->validated());

        return new BookWithAuthorResource($updatedBook);
    }

    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json(null, 204);
    }
}
