<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use App\Imports\CompanyImport;
use App\Exports\CompaniesExport;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Company\StoreRequest;
use Illuminate\Database\Eloquent\Collection;

class CompanyController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;

        $companies = Company::when($status === "inactive", function (
            $query
        ) use ($status) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($companies->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        // foreach (Company::lazy() as $companies) {
        //     $companies = Company::get();
        // }
        return $this->responseSuccess(ResponseMessage::DISPLAY, $companies);
    }
    public function companies(Request $request)
    {
        $companies = Company::withTrashed()
            ->useFilters()
            ->dynamicPaginate();

        if ($companies->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $companies);
    }
    public function show($id)
    {
        $company = Company::find($id);
        if (!$company) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $company);
    }

    public function store(StoreRequest $request)
    {
        $company = Company::create([
            "code" => $request->code,
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Company Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $company);
    }

    public function update(StoreRequest $request, $id)
    {
        $company = Company::find($id);
        if (!$company) {
            return $this->responseNotFound("Nothing to update.");
        }

        $company->update([
            "code" => $request->code,
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $company);
    }
    public function destroy($id)
    {
        $company = Company::where("id", $id)
            ->withTrashed()
            ->get();

        if ($company->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $company = Company::withTrashed()->find($id);
        $is_active = Company::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $company->delete();
            return $this->responseDeleted();
        } else {
            $company->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $company);
    }
    public function export()
    {
        $export = Excel::download(new CompaniesExport(), "company.csv");

        if (!$export) {
            return $this->responseUnprocessable("Export failed.");
        }

        return $this->responseSuccess(ResponseMessage::EXPORT);
    }

    public function import(Request $request)
    {
        $company_file = $request->file("file");

        if (!$company_file) {
            return $this->responseUnprocessable("File not found.");
        }
        Excel::import(new CompanyImport(), $company_file);

        return $this->responseSuccess(ResponseMessage::IMPORT);
    }
}
