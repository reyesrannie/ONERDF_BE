<?php

namespace App\Exports;

use App\Models\BusinessUnit;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class BusinessUnitExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return BusinessUnit::with("company")
            ->whereNull("deleted_at")
            ->get();
    }
    public function headings(): array
    {
        return ["Code", "Name", "Created At"];
    }
    public function map($business_unit): array
    {
        return [
            $business_unit->code,
            $business_unit->name,
            $business_unit->company && $business_unit->company->name
                ? $business_unit->company->name
                : "Deleted company.",
            $business_unit->created_at->format("Y-m-d H:i"),
        ];
    }
}
