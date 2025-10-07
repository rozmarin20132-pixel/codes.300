<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id;

        return [
            'company_id' => ['required','exists:companies,id'],
            'first_name' => ['required','string','max:100'],
            'last_name' => ['required','string','max:100'],
            'email' => ['required','email','max:255','unique:employees,email'.($employeeId ? ",$employeeId" : '')],
            'phone' => ['nullable','string','max:30'],
        ];
    }
}
