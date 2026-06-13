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

    // public function showPaymentPage($ref)
    // {
    //     $payment = Payment::where('merchant_ref', $ref)->firstOrFail();

    //     return view('payment.pay', compact('payment'));
    // }

    public function showPaymentPage($ref)
    {
        $payment = Payment::where('merchant_ref', $ref)->firstOrFail();

        $mid = env('MOAMALAT_MID');
        $tid = env('MOAMALAT_TID');
        $secret = env('MOAMALAT_SECRET');

        $amountInDirhams = $payment->amount * 1000; // تحويل للدينار إلى درهم

        // التوقيت بـ 12 خانة فقط بالملي طبقاً للتوثيق: YYYYMMDDHHMM
        $dateTime = date('YmdHi');

        // --- تجربة الطريقة الأولى: استخدام السيكرت كمفتاح نصي مباشر (Plain Text) وهو الأغلب في حسابات الـ Live الحركية
        $decryptedSecret = $secret;

        // أو لو البنك قايلك إنه Base64 جرب السطر اللي تحته:
        // $decryptedSecret = base64_decode($secret);

        // بناء السلسلة الأبجدية الصارمة بالحرف من الدوكيومنتيشن الجديدة:
        $stringToHash = "Amount=$amountInDirhams&DateTimeLocalTrxn=$dateTime&MerchantId=$mid&MerchantReference=$ref&TerminalId=$tid";

        // إنتاج الـ Hash وتحويله لحروف كبيرة
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