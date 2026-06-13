<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;

use App\Models\Wallet\Payment;
use App\Models\Wallet\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $ref = 'ORDER-' . time();

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

        return view('payment.pay', compact('payment'));
    }

    public function callback(Request $request)
    {
        $payment = Payment::where('merchant_ref', $request->MerchantReference)->first();

        if (!$payment) {
            return response()->json(['message' => 'Order Not Found'], 404);
        }

        // نجاح الدفع
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
}