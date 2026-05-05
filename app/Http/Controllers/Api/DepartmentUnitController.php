<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditTrail;
use Illuminate\Http\Request;
use App\Models\DepartmentUnit;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\DepartmentUnit\StoreRequest;

class DepartmentUnitController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $department_unit = DepartmentUnit::when(
            $status === "inactive",
            function ($query) use ($status) {
                return $query->onlyTrashed();
            }
        )
            ->useFilters()
            ->dynamicPaginate();

        if ($department_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $department_unit
        );
    }
    public function department_unit(Request $request)
    {
        $status = $request->status;
        $department_unit = DepartmentUnit::withTrashed()
            ->useFilters()
            ->dynamicPaginate();

        if ($department_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $department_unit
        );
    }
    public function show($id)
    {
        $department_unit = DepartmentUnit::find($id);
        if (!$department_unit) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $department_unit
        );
    }

    public function store(StoreRequest $request)
    {
        $department_unit = DepartmentUnit::create([
            "code" => $request->code,
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Department Unit Module",
        //     "details" => "created account " . $request->full_name,
        // ]);

        return $this->responseCreated(
            ResponseMessage::CREATE,
            $department_unit
        );
    }
    public function update(Request $request, $id)
    {
        $department_unit = DepartmentUnit::find($id);
        if (!$department_unit) {
            return $this->responseNotFound("Nothing to update.");
        }

        $department_unit->update([
            "code" => $request->code,
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(
            ResponseMessage::UPDATE,
            $department_unit
        );
    }
    public function destroy($id)
    {
        $department_unit = DepartmentUnit::where("id", $id)
            ->withTrashed()
            ->get();

        if ($department_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $department_unit = DepartmentUnit::withTrashed()->find($id);
        $is_active = DepartmentUnit::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $department_unit->delete();
            return $this->responseDeleted();
        } else {
            $department_unit->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $department_unit);
    }
    public function import(Request $request)
    {
        $import = $request->all();
        $department_unit = DepartmentUnit::upsert($import, ["code"], ["name"]);

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
