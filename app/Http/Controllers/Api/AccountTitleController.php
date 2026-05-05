<?php

namespace App\Http\Controllers\Api;

use App\Models\AccountTitle;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\AccountTitleResource;
use App\Http\Resources\AccountTitleViewResource;
use App\Http\Requests\AccountTitle\ImportRequest;

class AccountTitleController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $account_title = AccountTitle::with(
            "account_group",
            "account_sub_group",
            "account_unit",
            "account_type",
            "financial_statement",
            "normal_balance",
            "credit",
            "allocation",
            "charge"
        )
            ->when($status === "inactive", function ($query) use ($status) {
                return $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        if ($account_title->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $account_title);
    }
    public function show($id)
    {
        $account_title = AccountTitle::find($id);
        if (!$account_title) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $account_title);
    }

    public function store(Request $request)
    {
        $account_title = AccountTitle::create([
            "code" => $request->code,
            "name" => $request->name,
            "account_group_id" => $request->account_group_id,
            "account_sub_group_id" => $request->account_sub_group_id,
            "account_unit_id" => $request->account_unit_id,
            "account_type_id" => $request->account_type_id,
            "financial_statement_id" => $request->financial_statement_id,
            "normal_balance_id" => $request->normal_balance_id,
            "credit_id" => $request->credit_id,
            "allocation_id" => $request->allocation_id,
            "charge" => $request->charge,
            "purchase_book" => $request->purchase_book,
            "vouchers_book" => $request->vouchers_book,
            "cash_disbursement_book" => $request->cash_disbursement_book,
            "sales_journal_book" => $request->sales_journal_book,
            "cash_receipt_book" => $request->cash_receipt_book,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "AccountTitle Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $account_title);
    }

    public function update(Request $request, $id)
    {
        $account_title = AccountTitle::find($id);
        if (!$account_title) {
            return $this->responseNotFound("Nothing to update.");
        }

        $account_title->update([
            "code" => $request->code,
            "name" => $request->name,
            "account_group_id" => $request->account_group_id,
            "account_sub_group_id" => $request->account_sub_group_id,
            "account_unit_id" => $request->account_unit_id,
            "account_type_id" => $request->account_type_id,
            "financial_statement_id" => $request->financial_statement_id,
            "normal_balance_id" => $request->normal_balance_id,
            "credit_id" => $request->credit_id,
            "allocation_id" => $request->allocation_id,
            "charge" => $request->charge,
            "purchase_book" => $request->purchase_book,
            "vouchers_book" => $request->vouchers_book,
            "cash_disbursement_book" => $request->cash_disbursement_book,
            "sales_journal_book" => $request->sales_journal_book,
            "cash_receipt_book" => $request->cash_receipt_book,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $account_title);
    }
    public function destroy($id)
    {
        $account_title = AccountTitle::where("id", $id)
            ->withTrashed()
            ->get();

        if ($account_title->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $account_title = AccountTitle::withTrashed()->find($id);
        $is_active = AccountTitle::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $account_title->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $account_title->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $account_title);
    }

    public function account_title_api(Request $request)
    {
        $status = $request->status;
        $account_title = AccountTitle::with(
            "account_group",
            "account_sub_group",
            "account_unit",
            "account_type",
            "financial_statement",
            "normal_balance",
            "credit",
            "allocation",
            "charge"
        )
            ->withTrashed()
            ->useFilters()
            ->dynamicPaginate();

        if ($account_title->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $collect = AccountTitleResource::collection($account_title);

        return $this->responseSuccess(ResponseMessage::DISPLAY, $collect);
    }
    public function import(ImportRequest $request)
    {
        $import = $request->all();
        $account_title = AccountTitle::upsert(
            $import,
            ["code"],
            [
                "name",
                "account_group_id",
                "account_sub_group_id",
                "account_unit_id",
                "account_type_id",
                "financial_statement_id",
                "normal_balance_id",
                "credit_id",
                "allocation_id",
                "charge_id",
                "purchase_book",
                "vouchers_book",
                "cash_disbursement_book",
                "sales_journal_book",
                "cash_receipt_book",
            ]
        );

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Import",
        //     "module" => "Account Title Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::IMPORT, $account_title);
    }
}
