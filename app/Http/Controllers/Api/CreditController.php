<?php

namespace App\Http\Controllers\Api;

use App\Models\Credit;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\Credit\ImportRequest;

class CreditController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;

        $credit = Credit::when($status === "inactive", function ($query) use (
            $status
        ) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($credit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $credit);
    }
    public function show($id)
    {
        $credit = Credit::find($id);
        if (!$credit) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $credit);
    }

    public function store(Request $request)
    {
        $credit = Credit::create([
            "code" => $request->code,
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Credit Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $credit);
    }

    public function update(Request $request, $id)
    {
        $credit = Credit::find($id);
        if (!$credit) {
            return $this->responseNotFound("Nothing to update.");
        }

        $credit->update([
            "code" => $request->code,
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $credit);
    }
    public function destroy($id)
    {
        $credit = Credit::where("id", $id)
            ->withTrashed()
            ->get();

        if ($credit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $credit = Credit::withTrashed()->find($id);
        $is_active = Credit::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $credit->delete();
            return $this->responseDeleted();
        } else {
            $credit->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $credit);
    }

    public function import(ImportRequest $request)
    {
        $import = $request->all();
        $department = Credit::upsert($import, ["code"], ["name"]);

        return $this->responseSuccess("Imported Sucessfully.", $import);
    }
}
