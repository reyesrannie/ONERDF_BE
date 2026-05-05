<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CityMunicipality;
use App\function\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;

class CityMunicipalityController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;

        $city_municipality = CityMunicipality::with("province")
            ->when($status === "inactive", function ($query) use ($status) {
                return $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        if ($city_municipality->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        return $this->responseSuccess(
            ResponseMessage::DISPLAY,
            $city_municipality
        );
    }
}
