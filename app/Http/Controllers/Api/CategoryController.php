<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\Category\StoreRequest;

class CategoryController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $category = Category::when($status === "inactive", function ($query) {
            return $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($category->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $category);
    }
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $category);
    }

    public function store(StoreRequest $request)
    {
        $category = Category::create([
            "name" => $request->name,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Category Module",
        //     "details" => "created account " . $request->full_name,
        // ]);
        return $this->responseCreated(ResponseMessage::CREATE, $category);
    }

    public function update(StoreRequest $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->responseNotFound("Nothing to update.");
        }

        $category->update([
            "name" => $request->name,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $category);
    }
    public function destroy($id)
    {
        $category = Category::where("id", $id)
            ->withTrashed()
            ->get();

        if ($category->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $category = Category::withTrashed()->find($id);
        $is_active = Category::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $category->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $category->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $category);
    }
}
