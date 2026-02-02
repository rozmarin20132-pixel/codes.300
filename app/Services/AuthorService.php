<?php

namespace App\Services;

use App\Filters\Authors\BookTitleSearch;
use App\Models\Author;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class AuthorService
{
    public function getFilteredAuthors(?string $search, int $perPage = 15): LengthAwarePaginator
    {
        return app(Pipeline::class)
            ->send(Author::query())
            ->through([
                BookTitleSearch::class,
            ])
            ->thenReturn()
            ->with(['books' => function ($query) use ($search) {
                $query->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"));
            }])
            ->paginate($perPage)
            ->withQueryString();
    }
}
