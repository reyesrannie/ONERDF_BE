<?php

namespace App\Http\Controllers\Api;

use App\Models\SubUnit;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\SubUnit\StoreRequest;

class SubUnitController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $sub_unit = SubUnit::when($status === "inactive", function (
            $query
        ) use ($status) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($sub_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $sub_unit);
    }
    public function sub_unit(Request $request)
    {
        $sub_unit = SubUnit::withTrashed()
            ->useFilters()
            ->dynamicPaginate();

        if ($sub_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $sub_unit);
    }
    public function show($id)
    {
        $sub_unit = SubUnit::find($id);
        if (!$sub_unit) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $sub_unit);
    }

    public function store(StoreRequest $request)
    {
        $sub_unit = SubUnit::create([
            "code" => $request->code,
            "name" => $request->name,
        ]);
        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Sub Unit Module",
        //     "details" => "created account " . $request->full_name,
        // ]);

        return $this->responseCreated(ResponseMessage::CREATE, $sub_unit);
    }
    public function update(Request $request, $id)
    {
        $sub_unit = SubUnit::find($id);
        if (!$sub_unit) {
            return $this->responseNotFound("Nothing to update.");
        }

        $sub_unit->update([
            "code" => $request->code,
            "name" => $request->name,

            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $sub_unit);
    }
    public function destroy($id)
    {
        $sub_unit = SubUnit::where("id", $id)
            ->withTrashed()
            ->get();

        if ($sub_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $sub_unit = SubUnit::withTrashed()->find($id);
        $is_active = SubUnit::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $sub_unit->delete();
            return $this->responseDeleted();
        } else {
            $sub_unit->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $sub_unit);
    }
    public function import(Request $request)
    {
        $import = $request->all();
        $sub_unit = SubUnit::upsert($import, ["code"], ["name", "deleted_at"]);

        return $this->responseSuccess("Imported Sucessfully.", $import);
    }

    // public function export()
    // {
    //     $export = Excel::download(
    //         new BusinessUnitExport(),
    //         "BusinessUnitMasterlist.xlsx"
    //     );

    //     if (!$export) {
    //         return $this->responseUnprocessable("Export failed.");
    //     }

    //     return $this->responseSuccess(ResponseMessage::EXPORT, $export);
    // }
}
