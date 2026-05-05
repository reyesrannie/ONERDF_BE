<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\SubMunicipality;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;

class SubMunicipalityController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;

        $sub_municipality = SubMunicipality::with("cityMunicipality")
            ->when($status === "inactive", function ($query) use ($status) {
                return $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        if ($sub_municipality->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $sub_municipality
        );
    }
}
