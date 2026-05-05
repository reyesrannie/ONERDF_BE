<?php

namespace App\Http\Controllers\Api;

use App\Models\AccountGroup;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\AccountGroup\ImportRequest;

class AccountGroupController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $account_group = AccountGroup::when($status === "inactive", function (
            $query
        ) use ($status) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($account_group->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $account_group);
    }
    public function show($id)
    {
        $account_group = AccountGroup::find($id);
        if (!$account_group) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $account_group);
    }

    public function store(Request $request)
    {
        $account_group = AccountGroup::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "AccountGroup Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $account_group);
    }

    public function update(Request $request, $id)
    {
        $account_group = AccountGroup::find($id);
        if (!$account_group) {
            return $this->responseNotFound("Nothing to update.");
        }

        $account_group->update([
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $account_group);
    }
    public function destroy($id)
    {
        $account_group = AccountGroup::where("id", $id)
            ->withTrashed()
            ->get();

        if ($account_group->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $account_group = AccountGroup::withTrashed()->find($id);
        $is_active = AccountGroup::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $account_group->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $account_group->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $account_group);
    }
    public function import(ImportRequest $request)
    {
        $import = $request->all();

        foreach ($import as $file_import) {
            $account_group = AccountGroup::create([
                "name" => $file_import["name"],
            ]);
        }

        return $this->responseCreated(ResponseMessage::CREATE, $import);
    }
}
