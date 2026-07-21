<?php

namespace App\Http\Controllers\Api;

use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\AuditTrailResource;
use App\Http\Resources\OTPLogsResource;
use App\Models\AuditTrail;
use App\Models\OtpLogs;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $audit = AuditTrail::with("system")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        if ($audit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        AuditTrailResource::collection($audit);

        return $this->responseSuccess(ResponseMessage::DISPLAY, $audit);
    }
    public function destroy($id)
    {
        $audit = AuditTrail::where("id", $id)
            ->withTrashed()
            ->get();

        if ($audit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $audit = AuditTrail::withTrashed()->find($id);
        $is_active = AuditTrail::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $audit->delete();
            $message = ResponseMessage::DELETE;
        } else {
            $audit->restore();
            $message = ResponseMessage::RESTORE;
        }
        $audit_collect = new AuditTrailResource($audit);
        return $this->responseSuccess($message, $audit_collect);
    }

    public function audit_logins(StatusRequest $request)
    {
        $status = $request->status;
        $audit = OtpLogs::with("user", "accessedBy")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->useFilters()
            ->latest()
            ->dynamicPaginate();

        if ($audit->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        OTPLogsResource::collection($audit);

        return $this->responseSuccess(ResponseMessage::DISPLAY, $audit);
    }
}
