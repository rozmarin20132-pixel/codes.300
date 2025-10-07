<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
        $companyId = $this->route('company')?->id;

        return [
            'name' => ['required','string','max:255'],
            'nip' => ['required','string','max:9', 'min:9', 'unique:companies,nip'.($companyId ? ",$companyId" : '')],
            'address' => ['required','string','max:255'],
            'city' => ['required','string','max:100'],
            'postal_code' => ['required','string','max:20'],
        ];
    }
}
