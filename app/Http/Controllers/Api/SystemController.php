<?php

namespace App\Http\Controllers\Api;

use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use App\Http\Requests\System\StoreRequest;
use App\Http\Resources\SystemResource;
use App\Models\AuditTrail;
use App\Models\System;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SystemController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = request()->status;
        $system = System::with("category")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        if ($system->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        SystemResource::collection($system);

        return $this->responseSuccess(ResponseMessage::DISPLAY, $system);
    }
    public function show($id)
    {
        $system = System::find($id)->dynamicPaginate();

        if (!$system) {
            return $this->responseNotFound("Nothing to display.");
        }

        SystemResource::collection($system);

        return $this->responseSuccess(ResponseMessage::DISPLAY, $system);
    }
    public function store(StoreRequest $request)
    {
        $convert_to_string = json_encode($request->slice);
        $system = System::create([
            "url_holder" => $request->url_holder,
            "token" => $request->token,
            "system_image" => $request->system_image,
            "system_background" => $request->system_background,
            "system_name" => $request->system_name,
            "description" => $request->description,
            "category_id" => $request->category_id,
            "backend_url" => $request->backend_url,
            "slice" => $convert_to_string,
        ]);

        $collect_result = new SystemResource($system);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "System Module",
        //     "details" => "created account " . $request->full_name,
        // ]);

        return $this->responseCreated(ResponseMessage::CREATE, $collect_result);
    }
    public function update(StoreRequest $request, $id)
    {
        $system = System::find($id);
        $convert_to_string = json_encode($request->slice);
        if (!$system) {
            return $this->responseNotFound("Nothing to display.");
        }
        $system->update([
            "url_holder" => $request->url_holder,
            "token" => $request->token,
            "system_image" => $request->system_image,
            "system_name" => $request->system_name,
            "system_background" => $request->system_background,
            "description" => $request->description,
            "backend_url" => $request->backend_url,
            "category_id" => $request->category_id,
            "slice" => $convert_to_string,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        $collect_result = new SystemResource($system);

        return $this->responseSuccess(ResponseMessage::UPDATE, $collect_result);
    }
    public function destroy($id)
    {
        $system = System::where("id", $id)
            ->withTrashed()
            ->get();

        if ($system->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $system = System::withTrashed()->find($id);
        $is_active = System::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $system->delete();
            return $this->responseDeleted();
        } else {
            $system->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $user_collect);
    }
    public function store_file(Request $request)
    {
        $extension = $request->file("file")->getClientOriginalExtension();
        $name = time() . "_" . Str::random(10) . "." . $extension;
        $path = $request->file("file")->storeAs("public/attachment", $name);

        return $this->responseSuccess(ResponseMessage::IMAGE, $path);
    }
    public function get_file($filename)
    {
        $disk = Storage::disk("public");
        $filePath = "attachment/{$filename}";

        if (!$disk->exists($filePath)) {
            return $this->responseNotFound("Nothing to display.");
        }

        $fullPath = $disk->path($filePath);
        return response()->download($fullPath);
    }
    public function delete_file($filename)
    {
        $disk = Storage::disk("public");
        $filePath = "attachment/{$filename}";

        if (!$disk->exists($filePath)) {
            return $this->responseNotFound("Nothing to display.");
        }
        $fullPath = $disk->path($filePath)->delete();

        return $this->responseDeleted();
    }
}
