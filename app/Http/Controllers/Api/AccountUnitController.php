<?php

namespace App\Http\Controllers\Api;

use App\Models\AccountUnit;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\AccountUnit\ImportRequest;

class AccountUnitController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $account_unit = AccountUnit::when($status === "inactive", function (
            $query
        ) use ($status) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($account_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $account_unit);
    }
    public function show($id)
    {
        $account_unit = AccountUnit::find($id);
        if (!$account_unit) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $account_unit);
    }

    public function store(Request $request)
    {
        $account_unit = AccountUnit::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "AccountUnit Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $account_unit);
    }

    public function update(Request $request, $id)
    {
        $account_unit = AccountUnit::find($id);
        if (!$account_unit) {
            return $this->responseNotFound("Nothing to update.");
        }

        $account_unit->update([
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $account_unit);
    }
    public function destroy($id)
    {
        $account_unit = AccountUnit::where("id", $id)
            ->withTrashed()
            ->get();

        if ($account_unit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $account_unit = AccountUnit::withTrashed()->find($id);
        $is_active = AccountUnit::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $account_unit->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $account_unit->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $account_unit);
    }
    public function import(ImportRequest $request)
    {
        $import = $request->all();

        foreach ($import as $file_import) {
            $account_type = AccountUnit::create([
                "name" => $file_import["name"],
            ]);
        }

        return $this->responseCreated(ResponseMessage::CREATE, $import);
    }
}
