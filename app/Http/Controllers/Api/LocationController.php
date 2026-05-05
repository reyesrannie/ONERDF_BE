<?php

namespace App\Http\Controllers\Api;

use App\Models\SubUnit;
use App\Models\Location;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use App\Models\LocationSubUnit;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\Location\StoreRequest;
use App\Http\Requests\Location\ImportRequest;

class LocationController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $ocation = Location::when($status === "inactive", function (
            $query
        ) use ($status) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($ocation->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $ocation);
    }
    public function location(StatusRequest $request)
    {
        $status = $request->status;
        $ocation = Location::withTrashed()
            ->useFilters()
            ->dynamicPaginate();

        if ($ocation->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $ocation);
    }
    public function show($id)
    {
        $ocation = Location::find($id);
        if (!$ocation) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $ocation);
    }

    public function store(StoreRequest $request)
    {
        $location = Location::create([
            "code" => $request->code,
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Location Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $location);
    }
    public function update(StoreRequest $request, $id)
    {
        $location = Location::find($id);
        if (!$location) {
            return $this->responseNotFound("Nothing to update.");
        }

        $location->update([
            "code" => $request->code,
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $location);
    }
    public function destroy($id)
    {
        $location = Location::where("id", $id)
            ->withTrashed()
            ->get();

        if ($location->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $location = Location::withTrashed()->find($id);
        $is_active = Location::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $location->delete();
            return $this->responseDeleted();
        } else {
            $location->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $location);
    }
    public function import(ImportRequest $request)
    {
        $import = $request->all();

        $location = Location::upsert($import, ["code"], ["name"]);

        return $this->responseSuccess("Imported Sucessfully.", $import);
    }
}
