<?php

namespace App\Http\Requests\SupplierReference;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        return [
            "name" => [
                "required",
                $this->route("id")
                    ? "unique:supplier_references,name," . $this->route("id")
                    : "unique:supplier_references,name",
            ],
        ];
    }
}
