<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Author extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'last_book_title'];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}
