<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentCallbackRequest;
use App\Http\Requests\Wallet\CardsRequest;
use App\Models\Wallet\Payment;
use App\Models\Wallet\Transaction;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function __construct(protected PaymentRepositoryInterface $paymentRepository)
    {
    }
    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $ref = substr(time(), -8);

        $payment = Payment::create([
            'merchant_ref' => $ref,
            'amount' => $request->amount,
            'user_id' => auth()->id() ?? 1,
            'status' => 'pending',
        ]);

        return response()->json([
            'payment_url' => url("/pay/$ref")
        ]);
    }


    public function showPaymentPage($ref)
    {
        $payment = Payment::where('merchant_ref', $ref)->firstOrFail();

        $mid = env('MOAMALAT_MID');
        $tid = env('MOAMALAT_TID');
        $secret = env('MOAMALAT_SECRET');

        $amountInDirhams = $payment->amount * 1000;

        $dateTime = date('YmdHi');

        $decryptedSecret = $secret;
        $stringToHash = "Amount=$amountInDirhams&DateTimeLocalTrxn=$dateTime&MerchantId=$mid&MerchantReference=$ref&TerminalId=$tid";
        $secureHash = strtoupper(hash_hmac('sha256', $stringToHash, $decryptedSecret));

        return view('payment.pay', [
            'mid' => $mid,
            'tid' => $tid,
            'amount' => $amountInDirhams,
            'ref' => $ref,
            'dateTime' => $dateTime,
            'secureHash' => $secureHash,
            'jsUrl' => env('MOAMALAT_JS_URL')
        ]);
    }

    public function callbacks(Request $request)
    {
        $payment = Payment::where('merchant_ref', $request->MerchantReference)->first();

        if (!$payment) {
            return response()->json(['message' => 'Order Not Found'], 404);
        }

        if (($request->Status ?? '') === 'Approved') {

            $payment->update([
                'status' => 'success'
            ]);

            $user = $payment->user;

            $user->wallet += $payment->amount;
            $user->save();

            Transaction::create([
                'user_id' => $payment->user_id,
                'amount' => $payment->amount,
                'type' => 'topup',
                'status' => 'completed',
                'reference' => $payment->id,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function callback(PaymentCallbackRequest $request)
    {
        $payment = $this->paymentRepository->callback($request->validated('merchant_ref'), json_decode($request->validated('getway_details'), true));
        return successResponse(__("messages.success"), $payment->getResource(), 200);
    }

    public function chargeWallet(CardsRequest $request)
    {
        $payment = $this->paymentRepository->chargeWallet($request->validated('payment_type'), $request->validated('cardnumber'));
        return successResponse(__("messages.success"), $payment, 200);
    }

    public function walletBalance()
    {
        $wallet = $this->paymentRepository->balance();
        return successResponse(__("messages.success"), $wallet, 200);
    }

    public function walletLog($start = null, $end = null)
    {
        $walletLog = $this->paymentRepository->walletLog($start, $end);
        return successResponse(__("messages.success"), $walletLog, 200);
    }
}