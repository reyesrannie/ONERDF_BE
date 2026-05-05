<?php

namespace App\Http\Requests\Account;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class ChangeRequest extends FormRequest
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
    public function rules()
    {
        return [
            "old_password" => "required",
            "password" => "required|confirmed",
            "password_confirmation" => "required",
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $id = Auth::id();
            $user = User::find($id);

            if (!Hash::check($this->get("old_password"), $user->password)) {
                $validator
                    ->errors()
                    ->add("old_password", "Invalid credentials.");
            }

            // $validator->errors()->add("custom", $this->route()->id);
        });
    }
}
