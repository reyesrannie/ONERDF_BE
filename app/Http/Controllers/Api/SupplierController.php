<?php

namespace App\Http\Controllers\Api;

use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use App\Http\Requests\Supplier\StoreRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Models\SupplierBufferSeverity;
use App\Models\SupplierReference;
use App\Models\SupplierSystems;
use App\Models\SupplierType;
use App\Models\System;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;

        $supplier = Supplier::with(
            "supplier_type",
            "supplier_buffer",
            "supplier_reference",
            "supplier_system.system"
        )
            ->when($status === "inactive", function ($query) use ($status) {
                return $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        if ($supplier->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        SupplierResource::collection($supplier);

        return $this->responseSuccess(ResponseMessage::DISPLAY, $supplier);
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $supplier);
    }

    public function store(StoreRequest $request)
    {
        $system = $request->system;
        $supplier = Supplier::create([
            "code" => $request->code,
            "name" => $request->name,
            "address" => $request->address,
            "terms" => $request->terms,
            "supplier_type_id" => $request->supplier_type_id,
            "supplier_buffer_id" => $request->supplier_buffer_id,
            "supplier_reference_id" => $request->supplier_reference_id,
        ]);

        foreach ($system as $supplier_system) {
            $tag = SupplierSystems::create([
                "system_id" => $supplier_system,
                "supplier_id" => $supplier->id,
            ]);
        }

        return $this->responseCreated(ResponseMessage::CREATE, $supplier);
    }

    public function update(StoreRequest $request, $id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return $this->responseNotFound("Nothing to update.");
        }

        $supplier->update([
            "code" => $request->code,
            "name" => $request->name,
            "address" => $request->address,
            "terms" => $request->terms,
            "supplier_type_id" => $request->supplier_type_id,
            "supplier_buffer_id" => $request->supplier_buffer_id,
            "supplier_reference_id" => $request->supplier_reference_id,

            // "last_update_by" => Auth::user()->full_name,
        ]);

        if ($request->has("system")) {
            SupplierSystems::where("supplier_id", $supplier->id)->forceDelete();

            foreach ($request->system as $systemId) {
                SupplierSystems::create([
                    "supplier_id" => $supplier->id,
                    "system_id" => $systemId,
                ]);
            }
        }

        return $this->responseSuccess(ResponseMessage::UPDATE, $supplier);
    }
    public function destroy($id)
    {
        $supplier = Supplier::where("id", $id)
            ->withTrashed()
            ->get();

        if ($supplier->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $supplier = Supplier::withTrashed()->find($id);
        $is_active = Supplier::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $supplier->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $supplier->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $supplier);
    }

    public function import(Request $request)
    {
        $suppliers = $request->all();

        if (!is_array($suppliers) || empty($suppliers)) {
            return $this->responseNotFound("Invalid payload format.");
        }

        $importedCount = 0;
        $failedRows = [];

        DB::beginTransaction();

        try {
            foreach ($suppliers as $index => $row) {
                $type = SupplierType::where(
                    "name",
                    $row["supplier_type"] ?? ""
                )->first();

                $buffer = SupplierBufferSeverity::where(
                    "name",
                    $row["buffer"] ?? ""
                )->first();

                $reference = SupplierReference::where(
                    "name",
                    $row["reference"] ?? ""
                )->first();

                if (!$type || !$buffer || !$reference) {
                    $failedRows[] = [
                        "row" => $index + 1,
                        "data" => $row["code"] ?? "Unknown Code",
                        "reason" =>
                            "Missing or invalid supplier_type, buffer, or reference name.",
                    ];
                    continue;
                }

                $supplier = Supplier::withTrashed()->updateOrCreate(
                    ["code" => $row["code"]],
                    [
                        "name" => $row["name"] ?? null,
                        "address" => $row["address"] ?? null,
                        "terms" => $row["terms"] ?? null,
                        "supplier_type_id" => $type->id,
                        "supplier_buffer_id" => $buffer->id,
                        "supplier_reference_id" => $reference->id,
                    ]
                );

                if ($supplier->trashed()) {
                    $supplier->restore();
                }

                if (isset($row["systems"]) && is_array($row["systems"])) {
                    $systemIds = System::whereIn("id", $row["systems"])->pluck(
                        "id"
                    );

                    SupplierSystems::where(
                        "supplier_id",
                        $supplier->id
                    )->forceDelete();

                    foreach ($systemIds as $systemId) {
                        SupplierSystems::create([
                            "supplier_id" => $supplier->id,
                            "system_id" => $systemId,
                        ]);
                    }
                }

                $importedCount++;
            }

            DB::commit();

            return $this->responseSuccess(
                "Successfully imported $importedCount suppliers.",
                [
                    "failed_rows" => $failedRows,
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseNotFound(
                "Something went wrong during the import process: " .
                    $e->getMessage()
            );
        }
    }
}
