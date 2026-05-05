<?php

namespace App\Http\Requests\Charging;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
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
        // $company_id = $this->input("*.company");
        // $business_unit_id = $this->input("*.business_unit");
        // $department_id = $this->input("*.department_id");
        // $department_unit_id = $this->input("*.department_unit");
        // $sub_unit_id = $this->input("*.sub_unit");
        // $location_id = $this->input("*.location");
        return [
            "*.code" => [
                "distinct",
                "unique:charging_of_accounts,code",
                // Rule::distinct("charging_of_accounts", "code")
                //     ->where("company_id", $company_id)
                //     ->where("business_unit_id", $business_unit_id)
                //     ->where("department_id", $department_id)
                //     ->where("department_unit_id", $department_unit_id)
                //     ->where("sub_unit_id", $sub_unit_id)
                //     ->where("location_id", $location_id),
            ],

            "*.company" => [
                "required",
                "exists:companies,name,deleted_at,NULL",
            ],
            "*.business_unit" => [
                "required",
                "exists:business_unit,name,deleted_at,NULL",
            ],
            "*.department" => [
                "required",
                "exists:department,name,deleted_at,NULL",
            ],
            "*.department_unit" => [
                "required",
                "exists:department_unit,name,deleted_at,NULL",
            ],
            "*.sub_unit" => [
                "required",
                "exists:sub_unit,name,deleted_at,NULL",
            ],
            "*.location" => [
                "required",
                "exists:location,name,deleted_at,NULL",
            ],
        ];
    }
}
