<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditTrail;
use App\Models\Department;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\Department\StoreRequest;
use App\Http\Requests\Department\ImportRequest;

class DepartmentController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $department = Department::when($status === "inactive", function (
            $query
        ) use ($status) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($department->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $department);
    }
    public function departments(Request $request)
    {
        $status = $request->status;
        $department = Department::withTrashed()
            ->useFilters()
            ->dynamicPaginate();

        if ($department->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $department);
    }
    public function show($id)
    {
        $department = Department::find($id);
        if (!$department) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $department);
    }

    public function store(StoreRequest $request)
    {
        $department = Department::create([
            "code" => $request->code,
            "name" => $request->name,
        ]);
        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Department Module",
        //     "details" => "created account " . $request->full_name,
        // ]);

        return $this->responseCreated(ResponseMessage::CREATE, $department);
    }
    public function update(Request $request, $id)
    {
        $department = Department::find($id);
        if (!$department) {
            return $this->responseNotFound("Nothing to update.");
        }

        $department->update([
            "code" => $request->code,
            "name" => $request->name,

            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $department);
    }
    public function destroy($id)
    {
        $department = Department::where("id", $id)
            ->withTrashed()
            ->get();

        if ($department->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $department = Department::withTrashed()->find($id);
        $is_active = Department::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $department->delete();
            return $this->responseDeleted();
        } else {
            $department->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $department);
    }
    public function import(ImportRequest $request)
    {
        $import = $request->all();
        $department = Department::upsert($import, ["code"], ["name"]);

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
