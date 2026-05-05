<?php

namespace App\Http\Requests\Supplier;

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
            "code" => [
                "required",
                $this->route()->supplier
                    ? "unique:suppliers,code," . $this->route()->supplier
                    : "unique:suppliers,code",
            ],
            "name" => ["required"],
            "address" => ["required"],
            "terms" => ["required", "integer", "min:1", "max:365"],
            "supplier_type_id" => ["required", "exists:supplier_types,id"],
            "supplier_buffer_id" => [
                "required",
                "exists:supplier_buffer_severity,id",
            ],
            "supplier_reference_id" => [
                "required",
                "exists:supplier_references,id",
            ],
        ];
    }
}
