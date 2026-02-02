<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    public function handle($request, Closure $next)
    {
        $value = request($this->filterName());

        if (is_null($value) || $value === '') {
            return $next($request);
        }

        return $this->applyFilter($next($request), $value);
    }

    abstract protected function filterName(): string;
    abstract protected function applyFilter(Builder $builder, $value): Builder;
}
