<?php

namespace App\Http\Controllers\Api;

use App\Models\Province;
use Illuminate\Http\Request;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;

class ProvinceController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;

        $province = Province::with("region")
            ->when($status === "inactive", function ($query) use ($status) {
                return $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        if ($province->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(ResponseMessage::DISPLAY, $province);
    }
}
