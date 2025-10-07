<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeRepositoryInterface $employeeRepository) {}


    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page');

        return EmployeeResource::collection($this->employeeRepository->paginate($perPage));
    }


    public function store(EmployeeRequest $request): EmployeeResource
    {
        $employee = $this->employeeRepository->create($request->validated());

        return new EmployeeResource($employee->load('company'));
    }


    public function show(Employee $employee): EmployeeResource
    {
        return new EmployeeResource($employee);
    }


    public function update(EmployeeRequest $request, Employee $employee): EmployeeResource
    {
        $employee = $this->employeeRepository->update($employee, $request->validated());

        return new EmployeeResource($employee->load('company'));
    }


    public function destroy(Employee $employee): JsonResponse
    {
        $employee->delete();

        return response()->json(null, 204);
    }
}
