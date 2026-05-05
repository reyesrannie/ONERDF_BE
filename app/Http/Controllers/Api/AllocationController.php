<?php

namespace App\Http\Controllers\Api;

use App\Models\Allocation;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\Allocation\StoreRequest;
use App\Http\Requests\Allocation\ImportRequest;

class AllocationController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $allocation = Allocation::when($status === "inactive", function (
            $query
        ) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($allocation->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $allocation);
    }
    public function show($id)
    {
        $allocation = Allocation::find($id);
        if (!$allocation) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $allocation);
    }

    public function store(StoreRequest $request)
    {
        $allocation = Allocation::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Allocation Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $allocation);
    }

    public function update(StoreRequest $request, $id)
    {
        $allocation = Allocation::find($id);
        if (!$allocation) {
            return $this->responseNotFound("Nothing to update.");
        }

        $allocation->update([
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $allocation);
    }
    public function destroy($id)
    {
        $allocation = Allocation::where("id", $id)
            ->withTrashed()
            ->get();

        if ($allocation->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $allocation = Allocation::withTrashed()->find($id);
        $is_active = Allocation::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $allocation->delete();
            return $this->responseDeleted();
        } else {
            $allocation->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $allocation);
    }
    public function import(ImportRequest $request)
    {
        $import = $request->all();

        foreach ($import as $file_import) {
            $allocation = Allocation::create([
                "name" => $file_import["name"],
            ]);
        }

        return $this->responseCreated(ResponseMessage::CREATE, $import);
    }
}
