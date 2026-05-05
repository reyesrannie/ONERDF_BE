<?php

namespace App\Imports;

use App\Models\Company;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CompanyImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Company([
            "code" => $row["code"],
            "name" => $row["name"],
        ]);
    }

    public function rules(): array
    {
        return [
            "*.code" => ["required", "unique:companies,code"],
            "*.name" => ["required"],
        ];
    }
    public function customValidationMessages()
    {
        return [
            "code.unique" => "this row # :attribute is already been taken",
            "code.required" => "this row # :attribute is empty",
        ];
    }
}
