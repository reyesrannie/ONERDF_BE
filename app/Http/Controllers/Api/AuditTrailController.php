<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditTrail;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\AuditTrailResource;

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
}
