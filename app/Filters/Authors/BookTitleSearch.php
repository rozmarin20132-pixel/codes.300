<?php

namespace App\Filters\Authors;

use App\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class BookTitleSearch extends Filter
{
    protected function filterName(): string
    {
        return 'search';
    }

    protected function applyFilter(Builder $builder, $value): Builder
    {
        return $builder->whereHas('books', function ($query) use ($value) {
            $query->where('title', 'like', "%{$value}%");
        });
    }
}
