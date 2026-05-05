<?php

namespace App\Http\Requests\SupplierBufferSeverity;

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
                $this->route()->supplier_buffer_severity
                    ? "unique:supplier_buffer_severity,name," .
                        $this->route()->supplier_buffer_severity
                    : "unique:supplier_buffer_severity,name",
            ],
        ];
    }
}
