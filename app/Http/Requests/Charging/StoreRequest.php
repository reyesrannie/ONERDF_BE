<?php

namespace App\Http\Requests\Charging;

use Illuminate\Validation\Rule;
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
        $company_id = $this->input("company_id");
        $business_unit_id = $this->input("business_unit_id");
        $department_id = $this->input("department_id");
        $department_unit_id = $this->input("department_unit_id");
        $sub_unit_id = $this->input("sub_unit_id");
        $location_id = $this->input("location_id");
        return [
            "code" => [
                "required",
                $this->route()->charging
                    ? "unique:charging_of_accounts,code," .
                        $this->route()->charging
                    : "unique:charging_of_accounts,code",
                Rule::unique("charging_of_accounts", "code")
                    ->ignore($this->route("charging"))
                    ->where("company_id", $company_id)
                    ->where("business_unit_id", $business_unit_id)
                    ->where("department_id", $department_id)
                    ->where("department_unit_id", $department_unit_id)
                    ->where("sub_unit_id", $sub_unit_id)
                    ->where("location_id", $location_id),
            ],
            "name" => "required",
            "company_id" => ["required", "exists:companies,id,deleted_at,NULL"],
            "business_unit_id" => [
                "required",
                "exists:business_unit,id,deleted_at,NULL",
            ],
            "department_id" => [
                "required",
                "exists:department,id,deleted_at,NULL",
            ],
            "department_unit_id" => [
                "required",
                "exists:department_unit,id,deleted_at,NULL",
            ],
            "sub_unit_id" => ["required", "exists:sub_unit,id,deleted_at,NULL"],
            "location_id" => ["required", "exists:location,id,deleted_at,NULL"],
        ];
    }
}
