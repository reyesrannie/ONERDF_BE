<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ExcelImportService
{
    /**
     * Handle the Excel import process generically.
     *
     * @param UploadedFile $file The uploaded Excel/CSV file.
     * @param object $importClass The Maatwebsite Import class instance.
     * @return array
     */
    public function execute(UploadedFile $file, object $importClass): array
    {
        try {
            // Execute the import
            Excel::import($importClass, $file);

            return [
                "status" => true,
                "message" => "Data imported successfully.",
                "errors" => [],
            ];
        } catch (ValidationException $e) {
            // Catch row-by-row validation failures
            $failures = $e->failures();
            $errorList = [];

            foreach ($failures as $failure) {
                $errorList[] = [
                    "row" => $failure->row(),
                    "attribute" => $failure->attribute(),
                    "errors" => $failure->errors(),
                ];
            }

            return [
                "status" => false,
                "message" => "Validation failed on some rows.",
                "errors" => $errorList,
            ];
        } catch (Exception $e) {
            // Catch general file or database exceptions
            Log::error("Excel Import Error: " . $e->getMessage());

            return [
                "status" => false,
                "message" =>
                    "An error occurred during import. Please check the file format.",
                "errors" => [$e->getMessage()],
            ];
        }
    }

    public function preview(UploadedFile $file, object $importClass): array
    {
        try {
            // toArray returns an array of sheets. We usually just want the first sheet [0].
            $data = Excel::toArray($importClass, $file)[0];

            return [
                "status" => true,
                "message" => "Data parsed for preview.",
                "data" => $data,
                "errors" => [],
            ];
        } catch (Exception $e) {
            Log::error("Excel Preview Error: " . $e->getMessage());

            return [
                "status" => false,
                "message" => "An error occurred while reading the file.",
                "errors" => [$e->getMessage()],
            ];
        }
    }
}
