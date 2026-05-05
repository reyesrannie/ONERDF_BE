<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Models\FinancialStatement;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\FinancialStatement\ImportRequest;

class FinancialStatementController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $financial_statement = FinancialStatement::when(
            $status === "inactive",
            function ($query) use ($status) {
                return $query->onlyTrashed();
            }
        )
            ->useFilters()
            ->dynamicPaginate();

        if ($financial_statement->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $financial_statement
        );
    }
    public function show($id)
    {
        $financial_statement = FinancialStatement::find($id);
        if (!$financial_statement) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $financial_statement
        );
    }

    public function store(Request $request)
    {
        $financial_statement = FinancialStatement::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "FinancialStatement Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(
            ResponseMessage::CREATE,
            $financial_statement
        );
    }

    public function update(Request $request, $id)
    {
        $financial_statement = FinancialStatement::find($id);
        if (!$financial_statement) {
            return $this->responseNotFound("Nothing to update.");
        }

        $financial_statement->update([
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(
            ResponseMessage::UPDATE,
            $financial_statement
        );
    }
    public function destroy($id)
    {
        $financial_statement = FinancialStatement::where("id", $id)
            ->withTrashed()
            ->get();

        if ($financial_statement->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $financial_statement = FinancialStatement::withTrashed()->find($id);
        $is_active = FinancialStatement::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $financial_statement->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $financial_statement->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $financial_statement);
    }
    public function import(ImportRequest $request)
    {
        $import = $request->all();

        foreach ($import as $file_import) {
            $financial_statement = FinancialStatement::create([
                "name" => $file_import["name"],
            ]);
        }

        return $this->responseCreated(ResponseMessage::CREATE, $import);
    }
}
