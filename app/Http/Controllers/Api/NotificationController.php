<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditTrail;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;

class NotificationController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = request()->status;
        $notification = Notification::when($status === "inactive", function (
            $query
        ) {
            $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if ($notification->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $notification);
    }
    public function show($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $notification);
    }
    public function store(Request $request)
    {
        $notification = Notification::create([
            "title" => $request->title,
            "memo_file" => $request->memo_file,
            "subtitle" => $request->subtitle,
            "content" => $request->content,
            "footer" => $request->footer,
        ]);

        // $user_login = Auth()->user()->id;
        // $audit_trail = AuditTrail::create([
        //     "user_id" => $user_login,
        //     "action" => "Create",
        //     "module" => "Notification Module",
        //     "details" => "created account " . $request->full_name,
        // ]);

        return $this->responseCreated(ResponseMessage::CREATE, $notification);
    }
    public function update(Request $request, $id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return $this->responseNotFound("Nothing to display.");
        }
        $notification->update([
            "title" => $request->title,
            "memo_file" => $request->memo_file,
            "subtitle" => $request->subtitle,
            "content" => $request->content,
            "footer" => $request->footer,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        return $this->responseSuccess(ResponseMessage::UPDATE, $notification);
    }
    public function destroy($id)
    {
        $notification = Notification::where("id", $id)
            ->withTrashed()
            ->get();

        if ($notification->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $notification = Notification::withTrashed()->find($id);
        $is_active = Notification::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $notification->delete();
            return $this->responseDeleted();
        } else {
            $notification->restore();
            $message = ResponseMessage::RESTORE;
        }

        return $this->responseSuccess($message, $notification);
    }
}
