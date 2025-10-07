<?php

namespace App\Repositories\Eloquent;


use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class CompanyRepository implements CompanyRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Company::with('employees')->paginate($perPage);
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }


    public function update(Company $company, array $data): Company
    {
        $company->update($data);
        return $company;
    }


    public function delete(int $id): void
    {
        $company = Company::findOrFail($id);
        $company->delete();
    }
}
