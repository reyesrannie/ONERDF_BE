<?php

namespace App\Http\Controllers\Api;

use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Item\StoreRequest;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\ItemSystem;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = request()->status;
        $item = Item::with("uom", "item_system")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        if ($item->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        ItemResource::collection($item);

        return $this->responseSuccess(ResponseMessage::DISPLAY, $item);
    }
    public function show($id)
    {
        $item = Item::find($id)->dynamicPaginate();

        if (!$item) {
            return $this->responseNotFound("Nothing to display.");
        }

        ItemResource::collection($item);

        return $this->responseSuccess(ResponseMessage::DISPLAY, $item);
    }
    public function store(StoreRequest $request)
    {
        $system = $request->systems;

        $item = Item::create([
            "code" => $request->code,
            "description" => $request->description,
            "uom_id" => $request->uom_id,
        ]);

        foreach ($system as $system_id) {
            $tag = ItemSystem::create([
                "system_id" => $system_id,
                "item_id" => $item->id,
            ]);
        }

        $item_collect = new ItemResource($item);

        return $this->responseCreated(ResponseMessage::CREATE, $item_collect);
    }
    public function update(Request $request, $id)
    {
        $item = Item::find($id);
        $system = $request->systems;
        if (!$item) {
            return $this->responseNotFound("Nothing to display.");
        }

        $system = $request["systems"];
        $newTagged = collect($system)
            ->pluck("system_id")
            ->toArray();

        $currentTagged = ItemSystem::where("item_id", $id)
            ->get()
            ->pluck("system_id")
            ->toArray();

        foreach ($currentTagged as $system_id) {
            if (!in_array($system_id, $newTagged)) {
                ItemSystem::where("item_id", $id)
                    ->where("system_id", $system_id)
                    ->delete();
            }
        }

        foreach ($system as $key => $value) {
            if (!in_array($value["system_id"], $currentTagged)) {
                ItemSystem::create([
                    "item_id" => $item->id,
                    "system_id" => $system[$key]["system_id"],
                ]);
            }
        }
        $item->update([
            "code" => $request->code,
            "name" => $request->name,
            "description" => $request->description,
            "uom_id" => $request->uom_id,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        $item_collect = new ItemResource($item);
        return $this->responseSuccess(ResponseMessage::UPDATE, $item_collect);
    }
    public function destroy($id)
    {
        $item = Item::where("id", $id)
            ->withTrashed()
            ->get();

        if ($item->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $item_model = Item::withTrashed()->find($id);
        $is_active = Item::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $item_model->delete();
            return $this->responseDeleted();
        } else {
            $item_model->restore();
            $message = ResponseMessage::RESTORE;
        }
        $item_collect = new ItemResource($item_model);
        return $this->responseSuccess($message, $item_collect);
    }
}
