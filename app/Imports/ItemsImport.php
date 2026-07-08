<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Item;
use App\Models\ItemSystem;
use App\Models\ItemAccountTitle;
use Illuminate\Support\Facades\DB;
use Exception; // Make sure to import Exception at the top

class ItemsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $uploadedSystemIds = $rows
            ->pluck("system_id")
            ->filter()
            ->unique()
            ->toArray();
        $uploadedAccountTitles = $rows
            ->pluck("account_title_code")
            ->filter()
            ->unique()
            ->toArray();
        $uploadedUomCodes = $rows
            ->pluck("uom_code")
            ->filter()
            ->unique()
            ->toArray();

        $validSystems = DB::table("systems")
            ->whereIn("id", $uploadedSystemIds)
            ->pluck("id")
            ->toArray();

        $validAccountTitles = DB::table("account_title")
            ->whereIn("code", $uploadedAccountTitles)
            ->pluck("id", "code")
            ->toArray();

        $validUoms = DB::table("uom")
            ->whereIn("code", $uploadedUomCodes)
            ->pluck("id", "code")
            ->toArray();

        $groupedItems = $rows->groupBy("code");

        DB::transaction(function () use (
            $groupedItems,
            $validSystems,
            $validAccountTitles,
            $validUoms
        ) {
            foreach ($groupedItems as $code => $itemRows) {
                $firstRow = $itemRows->first();
                $excelUomCode = $firstRow["uom_code"];

                $verifiedUomId = $validUoms[$excelUomCode] ?? null;

                if (is_null($verifiedUomId)) {
                    throw new Exception(
                        "Import Failed: UOM Code '{$excelUomCode}' does not exist. (Item Code: {$code}, Description: {$firstRow["description"]})"
                    );
                }

                $item = Item::updateOrCreate(
                    ["code" => $code],
                    [
                        "description" => $firstRow["description"],
                        "uom_id" => $verifiedUomId,
                    ]
                );

                $systemIdsToAttach = $itemRows
                    ->pluck("system_id")
                    ->unique()
                    ->filter(fn($id) => in_array($id, $validSystems));

                foreach ($systemIdsToAttach as $systemId) {
                    ItemSystem::firstOrCreate([
                        "item_id" => $item->id,
                        "system_id" => $systemId,
                    ]);
                }

                $accountTitleCodesFromExcel = $itemRows
                    ->pluck("account_title_code")
                    ->unique()
                    ->filter();

                foreach ($accountTitleCodesFromExcel as $excelAccountCode) {
                    if (isset($validAccountTitles[$excelAccountCode])) {
                        $actualAccountId =
                            $validAccountTitles[$excelAccountCode];

                        ItemAccountTitle::firstOrCreate([
                            "item_id" => $item->id,
                            "account_title_id" => $actualAccountId,
                        ]);
                    }
                }
            }
        });
    }
}
