<?php

namespace App\Http\Controllers\Api;

use App\Models\Uom;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\Uom\StoreRequest;

class UomController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $uom = Uom::when($status === "inactive", function ($query) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($uom->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $uom);
    }
    public function show($id)
    {
        $uom = Uom::find($id);
        if (!$uom) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $uom);
    }

    public function store(StoreRequest $request)
    {
        $uom = Uom::create([
            "code" => $request->code,
            "description" => $request->description,
            "is_integer" => $request->is_integer,
        ]);

        return $this->responseCreated(ResponseMessage::CREATE, $uom);
    }

    public function update(StoreRequest $request, $id)
    {
        $uom = Uom::find($id);
        if (!$uom) {
            return $this->responseNotFound("Nothing to update.");
        }

        $uom->update([
            "code" => $request->code,
            "description" => $request->description,
            "is_integer" => $request->is_integer,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $uom);
    }
    public function destroy($id)
    {
        $uom = Uom::where("id", $id)
            ->withTrashed()
            ->get();

        if ($uom->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $uom = Uom::withTrashed()->find($id);
        $is_active = Uom::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $uom->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $uom->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $uom);
    }
}
