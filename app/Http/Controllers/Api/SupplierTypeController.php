<?php

namespace App\Http\Controllers\Api;

use App\Models\SupplierType;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\SupplierType\StoreRequest;

class SupplierTypeController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $supplier_type = SupplierType::when($status === "inactive", function (
            $query
        ) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($supplier_type->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $supplier_type);
    }
    public function show($id)
    {
        $supplier_type = SupplierType::find($id);
        if (!$supplier_type) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $supplier_type);
    }

    public function store(StoreRequest $request)
    {
        $supplier_type = SupplierType::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "SupplierType Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $supplier_type);
    }

    public function update(StoreRequest $request, $id)
    {
        $supplier_type = SupplierType::find($id);
        if (!$supplier_type) {
            return $this->responseNotFound("Nothing to update.");
        }

        $supplier_type->update([
            "name" => $request->name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $supplier_type);
    }
    public function destroy($id)
    {
        $supplier_type = SupplierType::where("id", $id)
            ->withTrashed()
            ->get();

        if ($supplier_type->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $supplier_type = SupplierType::withTrashed()->find($id);
        $is_active = SupplierType::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $supplier_type->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $supplier_type->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $supplier_type);
    }
}
