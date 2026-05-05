<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\NormalBalance;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\NormalBalance\ImportRequest;

class NormalBalanceController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $normal_balance = NormalBalance::when($status === "inactive", function (
            $query
        ) use ($status) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($normal_balance->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $normal_balance
        );
    }
    public function show($id)
    {
        $normal_balance = NormalBalance::find($id);
        if (!$normal_balance) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $normal_balance
        );
    }

    public function store(Request $request)
    {
        $normal_balance = NormalBalance::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "NormalBalance Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $normal_balance);
    }

    public function update(Request $request, $id)
    {
        $normal_balance = NormalBalance::find($id);
        if (!$normal_balance) {
            return $this->responseNotFound("Nothing to update.");
        }

        $normal_balance->update([
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $normal_balance);
    }
    public function destroy($id)
    {
        $normal_balance = NormalBalance::where("id", $id)
            ->withTrashed()
            ->get();

        if ($normal_balance->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $normal_balance = NormalBalance::withTrashed()->find($id);
        $is_active = NormalBalance::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $normal_balance->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $normal_balance->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $normal_balance);
    }
    public function import(ImportRequest $request)
    {
        $import = $request->all();

        foreach ($import as $file_import) {
            $financial_statement = NormalBalance::create([
                "name" => $file_import["name"],
            ]);
        }

        return $this->responseCreated(ResponseMessage::CREATE, $import);
    }
}
