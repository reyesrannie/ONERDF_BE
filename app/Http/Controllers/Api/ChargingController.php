<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\SubUnit;
use App\Models\Location;
use App\Models\AuditTrail;
use App\Models\Department;
use App\Models\BusinessUnit;
use Illuminate\Http\Request;
use App\Models\DepartmentUnit;
use App\function\ResponseMessage;
use App\Models\ChargingOfAccounts;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\ChargingResource;
use App\Http\Requests\Charging\StoreRequest;
use App\Http\Requests\Charging\ImportRequest;

class ChargingController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $charging = ChargingOfAccounts::with(
            "company",
            "business_unit",
            "department",
            "department_unit",
            "sub_unit",
            "location"
        )
            ->when($status === "inactive", function ($query) use ($status) {
                return $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        if ($charging->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        ChargingResource::collection($charging);

        return $this->responseSuccess(ResponseMessage::DISPLAY, $charging);
    }
    public function show($id)
    {
        $charging = ChargingOfAccounts::find($id);
        if (!$charging) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $charging);
    }

    public function store(StoreRequest $request)
    {
        $charging = ChargingOfAccounts::create([
            "code" => $request->code,
            "name" => $request->name,
            "company_id" => $request->company_id,
            "business_unit_id" => $request->business_unit_id,
            "department_id" => $request->department_id,
            "department_unit_id" => $request->department_unit_id,
            "sub_unit_id" => $request->sub_unit_id,
            "location_id" => $request->location_id,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Charging Accounts Module",
        //     "details" => "created account " . $request->full_name,
        // ]);

        return $this->responseCreated(ResponseMessage::CREATE, $charging);
    }

    public function update(StoreRequest $request, $id)
    {
        $charging = ChargingOfAccounts::find($id);
        if (!$charging) {
            return $this->responseNotFound("Nothing to update.");
        }

        $charging->update([
            "code" => $request->code,
            "name" => $request->name,
            "company_id" => $request->company_id,
            "business_unit_id" => $request->business_unit_id,
            "department_id" => $request->department_id,
            "department_unit_id" => $request->department_unit_id,
            "sub_unit_id" => $request->sub_unit_id,
            "location_id" => $request->location_id,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $charging);
    }
    public function destroy($id)
    {
        $charging = ChargingOfAccounts::where("id", $id)
            ->withTrashed()
            ->get();

        if ($charging->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $charging = ChargingOfAccounts::withTrashed()->find($id);
        $is_active = ChargingOfAccounts::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $charging->delete();
            return $this->responseDeleted();
        } else {
            $charging->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $charging);
    }
    public function export()
    {
        $export = Excel::download(new CompaniesExport(), "charging.csv");

        if (!$export) {
            return $this->responseUnprocessable("Export failed.");
        }

        return $this->responseSuccess(ResponseMessage::EXPORT);
    }

    public function import(ImportRequest $request)
    {
        $import = $request->all();

        foreach ($import as $index) {
            $company_name = $index["company"];
            $company = Company::where("name", $company_name)->first();

            $business_unit_name = $index["business_unit"];
            $business_unit = BusinessUnit::where(
                "name",
                $business_unit_name
            )->first();

            $department_name = $index["department"];
            $department = Department::where("name", $department_name)->first();

            $department_unit_name = $index["department_unit"];
            $department_unit = DepartmentUnit::where(
                "name",
                $department_unit_name
            )->first();

            $sub_unit_name = $index["sub_unit"];

            $sub_unit = SubUnit::where("name", $sub_unit_name)->first();

            $location_name = $index["location"];
            $location = Location::where("name", $location_name)->first();

            $location = ChargingOfAccounts::create([
                "code" => $index["code"],
                "name" => $index["name"],
                "company_id" => $company->id,
                "business_unit_id" => $business_unit->id,
                "department_id" => $department->id,
                "department_unit_id" => $department_unit->id,
                "sub_unit_id" => $sub_unit->id,
                "location_id" => $location->id,
            ]);
        }

        return $this->responseSuccess("Imported Sucessfully.", $import);
    }

    public function charging_api(Request $request)
    {
        $charging = ChargingOfAccounts::with(
            "company",
            "business_unit",
            "department",
            "department_unit",
            "sub_unit",
            "location"
        )
            ->withTrashed()
            ->useFilters()
            ->dynamicPaginate();

        if ($charging->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $charging_collect = ChargingResource::collection($charging);

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $charging_collect
        );
    }
}
