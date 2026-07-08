<?php

namespace App\Http\Controllers\Api;

use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Item\StoreRequest;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\ItemResource;
use App\Imports\ItemsImport;
use App\Models\Item;
use App\Models\ItemAccountTitle;
use App\Models\ItemSystem;
use Essa\APIToolKit\Api\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = request()->status;
        $item = Item::with(
            "uom",
            "item_system.system",
            "item_account_titles.account_title"
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

        foreach ($request->account_title as $account_title_id) {
            $tag = ItemAccountTitle::create([
                "account_title_id" => $account_title_id,
                "item_id" => $item->id,
            ]);
        }

        $item_collect = new ItemResource($item);

        return $this->responseCreated(ResponseMessage::CREATE, $item_collect);
    }
    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return $this->responseNotFound("Nothing to display.");
        }

        $item->update([
            "code" => $request->code,
            "description" => $request->description,
            "uom_id" => $request->uom_id,
        ]);

        $systems = $request->systems ?? [];

        ItemSystem::where("item_id", $id)
            ->whereNotIn("system_id", $systems)
            ->delete();

        $currentSystems = ItemSystem::where("item_id", $id)
            ->pluck("system_id")
            ->toArray();

        foreach ($systems as $system_id) {
            if (!in_array($system_id, $currentSystems)) {
                ItemSystem::create([
                    "item_id" => $item->id,
                    "system_id" => $system_id,
                ]);
            }
        }

        $accountTitles = $request->account_title ?? [];

        ItemAccountTitle::where("item_id", $id)
            ->whereNotIn("account_title_id", $accountTitles)
            ->delete();

        $currentAccountTitles = ItemAccountTitle::where("item_id", $id)
            ->pluck("account_title_id")
            ->toArray();

        foreach ($accountTitles as $account_title_id) {
            if (!in_array($account_title_id, $currentAccountTitles)) {
                ItemAccountTitle::create([
                    "item_id" => $item->id,
                    "account_title_id" => $account_title_id,
                ]);
            }
        }

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
        $request->validate([
            "file" => "required|mimes:xlsx,xls,csv",
        ]);

        try {
            Excel::import(new ItemsImport(), $request->file("file"));

            return $this->responseSuccess(ResponseMessage::IMPORT);
        } catch (Exception $e) {
            return $this->responseNotFound(
                $e->getMessage(),
                ResponseMessage::IMPORTFAILED
            );
        }
    }
}
