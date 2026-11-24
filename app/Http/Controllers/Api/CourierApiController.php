<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CourierApiService;
use Illuminate\Http\Request;

class CourierApiController extends Controller
{
    protected CourierApiService $courierService;

    public function __construct(CourierApiService $courierService)
    {
        $this->courierService = $courierService;
    }

    /**
     * POST /api/courier/cost
     */
    public function calculateCost(Request $request)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'weight' => 'nullable|integer|min:1',
            'courier' => 'nullable|string',
        ]);

        $rates = $this->courierService->calculateShippingCost(
            $request->origin,
            $request->destination,
            $request->weight ?? 1000,
            $request->courier
        );

        return response()->json([
            'success' => true,
            'data' => $rates,
        ]);
    }

    /**
     * GET /api/courier/track/{waybill}
     */
    public function track(Request $request, $waybill)
    {
        $courier = $request->query('courier');
        $trackingData = $this->courierService->trackWaybill($waybill, $courier);

        return response()->json($trackingData);
    }

    /**
     * POST /api/courier/generate-waybill
     */
    public function generateWaybill(Request $request)
    {
        $request->validate([
            'courier' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        $waybill = $this->courierService->generateWaybill(
            $request->courier,
            $request->reference,
            $request->shipper ?? [],
            $request->receiver ?? []
        );

        return response()->json([
            'success' => true,
            'data' => $waybill,
        ]);
    }

    /**
     * POST /api/courier/book-pickup
     */
    public function bookPickup(Request $request)
    {
        $request->validate([
            'courier' => 'required|string',
            'sender_name' => 'required|string',
            'sender_phone' => 'required|string',
            'address' => 'required|string',
            'time_slot' => 'nullable|string',
        ]);

        $pickup = $this->courierService->bookPickup($request->all());

        return response()->json($pickup);
    }
}
