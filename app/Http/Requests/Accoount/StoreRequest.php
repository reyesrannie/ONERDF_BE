<?php

namespace App\Http\Requests\Accoount;

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
        $id_prefix = $this->input("id_prefix");

        return [
            "id_prefix" => ["required"],
            "id_no" => [
                "required",
                Rule::unique("users")->where(function ($query) use (
                    $id_prefix
                ) {
                    return $query->where("id_prefix", $id_prefix);
                }),
            ],
            "first_name" => "required",
            // "middle_name" => "required",
            "last_name" => "required",
            "username" => [
                "required",
                $this->route()->user
                    ? "unique:users,username," . $this->route()->user
                    : "unique:users,username",
            ],
            "password" => "required",
            "access_permission" => "required",
        ];
    }
}
