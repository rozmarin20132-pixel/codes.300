<?php

namespace App\Repositories\Interfaces;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Company;
    public function update(Company $company, array $data): Company;
    public function delete(int $id): void;
}
