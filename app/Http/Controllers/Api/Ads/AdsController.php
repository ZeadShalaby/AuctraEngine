<?php

namespace App\Http\Controllers\Api\Ads;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ads\AllRequest;
use App\Http\Requests\Ads\CreateRequest;
use App\Http\Resources\AdPaymentResource;
use App\Http\Resources\AdsResource;
use App\Repositories\Interfaces\AdsRepositoryInterface;
use Illuminate\Http\Request;

class AdsController extends Controller
{

    public function __construct(protected AdsRepositoryInterface $adsRepo){}

    public function index(AllRequest $request)
    {
        $data = $request->validated();
        $ads = $this->adsRepo->all($data['status'] ?? null, $data['start'] ?? null, $data['end'] ?? null, $data['type'] ?? null);
        return successResponse(__("messages.success"), AdsResource::collection($ads), 200);
    }

    public function show(int $id)
    {
        $ad = $this->adsRepo->find($id);
        return successResponse(__("messages.success"), new AdsResource($ad), 200);
    }

    public function create(CreateRequest $request)
    {
        $payment = $this->adsRepo->create($request->validated());
        $responseData = new AdPaymentResource($payment);
        if ($payment->status === PaymentStatus::COMPLETED->value) {
            return successResponse(__("messages.payment_success_wallet"), $responseData, 201);
        }
        return successResponse(__("messages.created_api_goto_gateway"), $responseData, 201);
    }

    public function update(Request $request, int $id)
    {
        $ads = $this->adsRepo->update($request->validated(), $id);
        return successResponse(__("messages.success"), new AdPaymentResource($ads), 200);
    }

    public function destroy(int $id)
    {
        $this->adsRepo->delete($id);
        return successResponse(__("messages.success"), [], 200);
    }

    public function callback(Request $request)
    {
        $merchantRef = $request->input('merchant_ref');
        $gatewayDetails = $request->input('getway_details');
        if (is_string($gatewayDetails)) {
            $gatewayDetails = json_decode($gatewayDetails, true);
        }
        $payment = $this->adsRepo->callback($merchantRef, $gatewayDetails);
        return successResponse(__("messages.success"), new AdPaymentResource($payment), 200);
    }

}
