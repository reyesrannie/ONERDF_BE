<?php

namespace App\Http\Controllers\Api;

use App\Models\Charge;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\Charge\StoreRequest;
use App\Http\Requests\Charge\ImportRequest;

class ChargeController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $charge = Charge::when($status === "inactive", function ($query) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($charge->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $charge);
    }
    public function show($id)
    {
        $charge = Charge::find($id);
        if (!$charge) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $charge);
    }

    public function store(StoreRequest $request)
    {
        $charge = Charge::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Charge Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $charge);
    }

    public function update(StoreRequest $request, $id)
    {
        $charge = Charge::find($id);
        if (!$charge) {
            return $this->responseNotFound("Nothing to update.");
        }

        $charge->update([
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $charge);
    }
    public function destroy($id)
    {
        $charge = Charge::where("id", $id)
            ->withTrashed()
            ->get();

        if ($charge->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $charge = Charge::withTrashed()->find($id);
        $is_active = Charge::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $charge->delete();
            return $this->responseDeleted();
        } else {
            $charge->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $charge);
    }

    public function import(ImportRequest $request)
    {
        $import = $request->all();

        foreach ($import as $file_import) {
            $charge = Charge::create([
                "name" => $file_import["name"],
            ]);
        }

        return $this->responseCreated(ResponseMessage::CREATE, $import);
    }
}
