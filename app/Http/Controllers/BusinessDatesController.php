<?php

namespace App\Http\Controllers;

use App\DataSources\HolidaysDumbSource;
use App\Tools\BusinessDatesCalc;
use App\Tools\BusinessDatesCalcInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BusinessDatesController extends Controller
{
    /**
     * @param Request $request
     * @param BusinessDatesCalcInterface $businessDatesCalc
     * @return \Illuminate\Http\JsonResponse
     */
    function getBusinessDateWithDelay(Request $request, BusinessDatesCalcInterface $businessDatesCalc)
    {
        // todo: errors reporting / handling

        $data = $request->json();
        // todo: VALIDATE THIS, maybe use DTO

        $date = $data->get('initialDate');
        $delay = $data->get('delay');

        $initialQuery = [
            'initialDate' => $date,
            'delay' => $delay
        ];

        try {
            $results = $businessDatesCalc->calculateBusinessDate(
                new \DateTime($date),
                $delay
            );
            $response = $businessDatesCalc->prepareResponse(true, $initialQuery, $results);
            Log::debug($response);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = $businessDatesCalc->prepareResponse(false, $initialQuery, null, "An error occured");
            Log::error(array_merge($response, ['exception' => $e->getCode() . ':' . $e->getMessage()]));

            return response()->json($response, 500);
        }

    }
}
