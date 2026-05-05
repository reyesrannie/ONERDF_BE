<?php

namespace App\Http\Controllers\Api;

use App\Models\Column;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\Column\StoreRequest;

class ColumnController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $column = Column::when($status === "inactive", function ($query) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($column->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $column);
    }
    public function show($id)
    {
        $column = Column::find($id);
        if (!$column) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $column);
    }

    public function store(StoreRequest $request)
    {
        $column = Column::create([
            "name" => $request->name,
        ]);

        return $this->responseCreated(ResponseMessage::CREATE, $column);
    }

    public function update(StoreRequest $request, $id)
    {
        $column = Column::find($id);
        if (!$column) {
            return $this->responseNotFound("Nothing to update.");
        }

        $column->update([
            "name" => $request->name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $column);
    }
    public function destroy($id)
    {
        $column = Column::where("id", $id)
            ->withTrashed()
            ->get();

        if ($column->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $column = Column::withTrashed()->find($id);
        $is_active = Column::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $column->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $column->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $column);
    }
}
