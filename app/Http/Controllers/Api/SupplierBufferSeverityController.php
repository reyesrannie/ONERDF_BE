<?php

namespace App\Http\Controllers\Api;

use App\Models\SupplierBufferSeverity;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\SupplierBufferSeverity\StoreRequest;

class SupplierBufferSeverityController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $supplier_type = SupplierBufferSeverity::when(
            $status === "inactive",
            function ($query) {
                return $query->onlyTrashed();
            }
        )
            ->useFilters()
            ->dynamicPaginate();

        if ($supplier_type->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $supplier_type);
    }
    public function show($id)
    {
        $supplier_type = SupplierBufferSeverity::find($id);
        if (!$supplier_type) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $supplier_type);
    }

    public function store(StoreRequest $request)
    {
        $supplier_type = SupplierBufferSeverity::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "SupplierBufferSeverity Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $supplier_type);
    }

    public function update(Request $request, $id)
    {
        $supplier_type = SupplierBufferSeverity::find($id);
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
        $supplier_type = SupplierBufferSeverity::where("id", $id)
            ->withTrashed()
            ->get();

        if ($supplier_type->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $supplier_type = SupplierBufferSeverity::withTrashed()->find($id);
        $is_active = SupplierBufferSeverity::withTrashed()
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
