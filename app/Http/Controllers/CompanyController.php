<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class CompanyController extends Controller
{
    public function __construct(private readonly CompanyRepositoryInterface $companyRepository)
    {
    }


    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int)$request->query('per_page');

        return CompanyResource::collection($this->companyRepository->paginate($perPage));
    }

    public function store(CompanyRequest $request): CompanyResource
    {
        $company = $this->companyRepository->create($request->validated());

        return new CompanyResource($company->load('employees'));
    }

    public function show(Company $company): CompanyResource
    {
        return new CompanyResource($company);
    }

    public function update(CompanyRequest $request, Company $company): CompanyResource
    {
        $company = $this->companyRepository->update($company, $request->validated());

        return new CompanyResource($company->load('employees'));
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();
        return response()->json(null, 204);
    }
}
