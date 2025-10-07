<?php
namespace App\Repositories\Eloquent;


use App\Models\Employee;

use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Employee::with('company')->paginate($perPage);
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }


    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee;
    }


    public function delete(int $id): void
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
    }
}
