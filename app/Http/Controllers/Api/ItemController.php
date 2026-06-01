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
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = request()->status;
        $item = Item::with(
            "uom",
            "item_system.system",
            "account_titles.account_title"
        )
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
        $system = $request->systems; // [1, 2]

        if (!$item) {
            return $this->responseNotFound("Nothing to display.");
        }

        $currentTagged = ItemSystem::where("item_id", $id)
            ->pluck("system_id")
            ->toArray();

        foreach ($currentTagged as $system_id) {
            if (!in_array($system_id, $system)) {
                ItemSystem::where("item_id", $id)
                    ->where("system_id", $system_id)
                    ->delete();
            }
        }

        foreach ($system as $new_system_id) {
            if (!in_array($new_system_id, $currentTagged)) {
                ItemSystem::create([
                    "item_id" => $item->id,
                    "system_id" => $new_system_id,
                ]);
            }
        }

        $item->update([
            "code" => $request->code,
            "name" => $request->name,
            "description" => $request->description,
            "uom_id" => $request->uom_id,
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
            $message = ResponseMessage::DELETE;
        } else {
            $item_model->restore();
            $message = ResponseMessage::RESTORE;
        }
        $item_collect = new ItemResource($item_model);
        return $this->responseSuccess($message, $item_collect);
    }

    public function importSync(Request $request)
    {
        // 1. Validate incoming payload
        $request->validate([
            "*" => "required|array",
            "*.code" => "required|string",
            "*.description" => "required|string",
            "*.uom_id" => "required|integer",
            "*.systems" => "nullable|array",
        ]);

        $payload = $request->all();
        $newItemsProcessed = [];
        $existingItemsProcessed = [];

        // 2. Extract all 'codes' to find existing items in one single query
        $codes = collect($payload)
            ->pluck("code")
            ->filter()
            ->toArray();

        // 3. Pre-fetch existing items from DB to prevent N+1 query performance issues
        $existingItemsInDb = Item::whereIn("code", $codes)
            ->get()
            ->keyBy("code");

        // Pre-fetch all current system tags for the existing items
        $existingItemIds = $existingItemsInDb->pluck("id")->toArray();
        $allExistingTags = ItemSystem::whereIn("item_id", $existingItemIds)
            ->get()
            ->groupBy("item_id");

        try {
            // 4. Start the Transaction
            DB::transaction(function () use (
                $payload,
                $existingItemsInDb,
                $allExistingTags,
                &$newItemsProcessed,
                &$existingItemsProcessed
            ) {
                foreach ($payload as $itemData) {
                    $code = $itemData["code"];
                    $payloadSystems = $itemData["systems"] ?? [];

                    // --- SCENARIO A: EXISTING ITEM ---
                    if ($existingItemsInDb->has($code)) {
                        $itemModel = $existingItemsInDb->get($code);

                        // Get currently tagged system IDs for this item
                        $currentlyTagged = $allExistingTags->has($itemModel->id)
                            ? $allExistingTags
                                ->get($itemModel->id)
                                ->pluck("system_id")
                                ->toArray()
                            : [];

                        // Compare payload systems vs currently tagged systems to find untagged ones
                        $untaggedSystems = array_diff(
                            $payloadSystems,
                            $currentlyTagged
                        );

                        if (!empty($untaggedSystems)) {
                            // Insert only the new missing systems
                            foreach ($untaggedSystems as $system_id) {
                                ItemSystem::create([
                                    "item_id" => $itemModel->id,
                                    "system_id" => $system_id,
                                ]);
                            }

                            // Format data for the response
                            $itemData["id"] = $itemModel->id;
                            $itemData["updated_system"] = array_values(
                                array_unique(
                                    array_merge(
                                        $currentlyTagged,
                                        $untaggedSystems
                                    )
                                )
                            );

                            $existingItemsProcessed[] = $itemData;
                        }
                    }
                    // --- SCENARIO B: NEW ITEM ---
                    else {
                        $newItem = Item::create([
                            "code" => $itemData["code"],
                            "description" => $itemData["description"],
                            "uom_id" => $itemData["uom_id"],
                        ]);

                        if (!empty($payloadSystems)) {
                            foreach ($payloadSystems as $system_id) {
                                ItemSystem::create([
                                    "item_id" => $newItem->id,
                                    "system_id" => $system_id,
                                ]);
                            }
                        }

                        // Format data for the response
                        $itemData["id"] = $newItem->id;
                        $itemData["updated_system"] = $payloadSystems;

                        $newItemsProcessed[] = $itemData;
                    }
                }
            }); // End Transaction

            return $this->responseSuccess(ResponseMessage::IMPORT, [
                "existing_items" => $existingItemsProcessed,
                "new_items" => $newItemsProcessed,
            ]);
        } catch (\Exception $e) {
            // Because we wrapped DB::transaction in a try-catch, it automatically rolls back!
            return $this->responseBadRequest(
                "Import failed: " . $e->getMessage()
            );
        }
    }
}
