<?php

namespace App\Http\Controllers\Api;

use App\Models\AccountType;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\AccountType\ImportRequest;

class AccountTypeController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $account_type = AccountType::when($status === "inactive", function (
            $query
        ) use ($status) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($account_type->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $account_type);
    }
    public function show($id)
    {
        $account_type = AccountType::find($id);
        if (!$account_type) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $account_type);
    }

    public function store(Request $request)
    {
        $account_type = AccountType::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "AccountType Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $account_type);
    }

    public function update(Request $request, $id)
    {
        $account_type = AccountType::find($id);
        if (!$account_type) {
            return $this->responseNotFound("Nothing to update.");
        }

        $account_type->update([
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $account_type);
    }
    public function destroy($id)
    {
        $account_type = AccountType::where("id", $id)
            ->withTrashed()
            ->get();

        if ($account_type->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $account_type = AccountType::withTrashed()->find($id);
        $is_active = AccountType::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $account_type->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $account_type->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $account_type);
    }
    public function import(ImportRequest $request)
    {
        $import = $request->all();

        foreach ($import as $file_import) {
            $account_type = AccountType::create([
                "name" => $file_import["name"],
            ]);
        }

        return $this->responseCreated(ResponseMessage::CREATE, $import);
    }
}
