<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\AccountSubGroup;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\AccountSubGroup\ImportRequest;

class AccountSubGroupController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $account_sub_group = AccountSubGroup::when(
            $status === "inactive",
            function ($query) use ($status) {
                return $query->onlyTrashed();
            }
        )
            ->useFilters()
            ->dynamicPaginate();

        if ($account_sub_group->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $account_sub_group
        );
    }
    public function show($id)
    {
        $account_sub_group = AccountSubGroup::find($id);
        if (!$account_sub_group) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $account_sub_group
        );
    }

    public function store(Request $request)
    {
        $account_sub_group = AccountSubGroup::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "AccountSubGroup Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(
            ResponseMessage::CREATE,
            $account_sub_group
        );
    }

    public function update(Request $request, $id)
    {
        $account_sub_group = AccountSubGroup::find($id);
        if (!$account_sub_group) {
            return $this->responseNotFound("Nothing to update.");
        }

        $account_sub_group->update([
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(
            ResponseMessage::UPDATE,
            $account_sub_group
        );
    }
    public function destroy($id)
    {
        $account_sub_group = AccountSubGroup::where("id", $id)
            ->withTrashed()
            ->get();

        if ($account_sub_group->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $account_sub_group = AccountSubGroup::withTrashed()->find($id);
        $is_active = AccountSubGroup::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $account_sub_group->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $account_sub_group->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $account_sub_group);
    }

    public function import(ImportRequest $request)
    {
        $import = $request->all();

        foreach ($import as $file_import) {
            $account_group = AccountSubGroup::create([
                "name" => $file_import["name"],
            ]);
        }

        return $this->responseCreated(ResponseMessage::CREATE, $import);
    }
}
