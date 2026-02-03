<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorWithBookResource;
use App\Models\Author;
use App\Services\AuthorService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, AuthorService $authorService): AnonymousResourceCollection
    {
        return AuthorWithBookResource::collection(
            $authorService->getFilteredAuthors($request->query('search'))
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author): AuthorWithBookResource
    {
        return new AuthorWithBookResource($author->loadMissing('books'));
    }
}
