<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditTrail;
use App\Models\BusinessUnit;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Exports\BusinessUnitExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\BusinessUnit\StoreRequest;
use App\Http\Requests\BusinessUnit\ImportRequest;

class BusinessUnitController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $business_unit = BusinessUnit::when($status === "inactive", function (
            $query
        ) use ($status) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($business_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $business_unit);
    }
    public function business_unit(Request $request)
    {
        $status = $request->status;
        $business_unit = BusinessUnit::withTrashed()
            ->useFilters()
            ->dynamicPaginate();

        if ($business_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $business_unit);
    }
    public function show($id)
    {
        $business_unit = BusinessUnit::find($id);
        if (!$business_unit) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $business_unit);
    }

    public function store(StoreRequest $request)
    {
        $business_unit = BusinessUnit::create([
            "code" => $request->code,
            "name" => $request->name,
        ]);
        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Business Unit Module",
        //     "details" => "created account " . $request->full_name,
        // ]);

        return $this->responseCreated(ResponseMessage::CREATE, $business_unit);
    }
    public function update(Request $request, $id)
    {
        $business_unit = BusinessUnit::find($id);
        if (!$business_unit) {
            return $this->responseNotFound("Nothing to update.");
        }

        $business_unit->update([
            "code" => $request->code,
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $business_unit);
    }
    public function destroy($id)
    {
        $business_unit = BusinessUnit::where("id", $id)
            ->withTrashed()
            ->get();

        if ($business_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $business_unit = BusinessUnit::withTrashed()->find($id);
        $is_active = BusinessUnit::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $business_unit->delete();
            return $this->responseDeleted();
        } else {
            $business_unit->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $business_unit);
    }
    public function import(ImportRequest $request)
    {
        $import = $request->all();
        $business_unit = BusinessUnit::upsert($import, ["code"], ["name"]);

        return $this->responseSuccess("Imported Sucessfully.", $import);
    }

    public function export()
    {
        $export = Excel::download(
            new BusinessUnitExport(),
            "BusinessUnitMasterlist.xlsx"
        );

        if (!$export) {
            return $this->responseUnprocessable("Export failed.");
        }

        return $this->responseSuccess(ResponseMessage::EXPORT, $export);
    }
}
