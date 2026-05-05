<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CompaniesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;
    public function collection()
    {
        return Company::whereNull("deleted_at")->get();
    }
    public function headings(): array
    {
        return ["Code", "Name", "Created At"];
    }
    public function map($companies): array
    {
        return [
            $companies->code,
            $companies->name,
            $companies->created_at->format("Y-m-d H:i"),
        ];
    }
}
