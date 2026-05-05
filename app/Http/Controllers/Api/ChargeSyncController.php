<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use App\Models\ChargeSync;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Models\ChargingOfAccounts;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Support\Facades\Http;

class ChargeSyncController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $charge_sync = ChargeSync::when($status === "inactive", function (
            $query
        ) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($charge_sync->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $charge_sync);
    }
    public function show($id)
    {
        $charge_sync = ChargeSync::find($id);
        if (!$charge_sync) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $charge_sync);
    }

    public function store(Request $request)
    {
        $charge_sync = ChargeSync::create([
            "system_name" => $request->system_name,
            "url_holder" => $request->url_holder,
            "token" => $request->token,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "ChargeSync Module",
        //     "details" => "created account " . $request->full_name,
        // ]);

        return $this->responseCreated(ResponseMessage::CREATE, $charge_sync);
    }

    public function update(Request $request, $id)
    {
        $charge_sync = ChargeSync::find($id);
        if (!$charge_sync) {
            return $this->responseNotFound("Nothing to update.");
        }

        $charge_sync->update([
            "system_name" => $request->system_name,
            "url_holder" => $request->url_holder,
            "token" => $request->token,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $charge_sync);
    }
    public function destroy($id)
    {
        $charge_sync = ChargeSync::where("id", $id)
            ->withTrashed()
            ->get();

        if ($charge_sync->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $charge_sync = ChargeSync::withTrashed()->find($id);
        $is_active = ChargeSync::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $charge_sync->delete();
            return $this->responseDeleted();
        } else {
            $charge_sync->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $charge_sync);
    }

    public function sync_charge($id)
    {
        $system = ChargeSync::find($id);

        if (!$system) {
            return $this->responseNotFound("Nothing to display.");
        }

        $charging = ChargingOfAccounts::with(
            "company",
            "business_unit",
            "department",
            "department_unit",
            "sub_unit",
            "location"
        )
            ->withTrashed()
            ->get();

        foreach ($charging as $charging_sync) {
            $payload[] = [
                "sync_id" => $charging_sync->id,
                "code" => $charging_sync->code,
                "name" => $charging_sync->name,
                "company_id" => $charging_sync->company->id,
                "company_code" => $charging_sync->company->code,
                "company_name" => $charging_sync->company->name,
                "business_unit_id" => $charging_sync->business_unit->id,
                "business_unit_code" => $charging_sync->business_unit->code,
                "business_unit_name" => $charging_sync->business_unit->name,
                "department_id" => $charging_sync->department->id,
                "department_code" => $charging_sync->department->code,
                "department_name" => $charging_sync->department->name,
                "department_unit_id" => $charging_sync->department_unit->id,
                "department_unit_code" => $charging_sync->department_unit->code,
                "department_unit_name" => $charging_sync->department_unit->name,
                "sub_unit_id" => $charging_sync->sub_unit->id,
                "sub_unit_code" => $charging_sync->sub_unit->code,
                "sub_unit_name" => $charging_sync->sub_unit->name,
                "location_id" => $charging_sync->location->id,
                "location_code" => $charging_sync->location->code,
                "location_name" => $charging_sync->location->name,
                "deleted_at" => date($charging_sync->deleted_at),
            ];
        }

        $sync = Http::withOptions(["verify" => false])
            ->withHeaders(["api-key" => $system->token])
            ->post($system->url_holder, $payload);

        if ($sync->failed()) {
            return $this->responseConflictError(ResponseMessage::SERVER);
        }

        return $this->responseCreated(ResponseMessage::SYNC);
    }
}
