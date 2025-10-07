<?php
namespace App\Repositories\Interfaces;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


interface EmployeeRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Employee;
    public function update(Employee $employee, array $data): Employee;
    public function delete(int $id): void;
}
