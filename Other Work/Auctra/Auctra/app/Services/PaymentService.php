<?php 

namespace App\Services;

use App\Models\Wallet\Payment;

class PaymentService
{
    public function createPayment($user, $amount, $type)
    {
        $merchantRef = 'ORDER-' . time();

        //? 1. save pending payment
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'merchant_ref' => $merchantRef,
            'status' => 'pending',
            'type' => $type,
        ]);

        //? 2. build string to sign
        $mID = config('payment.mid');
        $tID = config('payment.tid');
        $key = config('payment.key');

        $dt = now()->format('YmdHis');

        $stringToHash =
            "Amount={$amount}000" .
            "&DateTimeLocalTrxn={$dt}" .
            "&MerchantId={$mID}" .
            "&MerchantReference={$merchantRef}" .
            "&TerminalId={$tID}";

        //? 3. generate secure hash (BACKEND ONLY 🔥)
        $secureHash = strtoupper(hash_hmac(
            'sha256',
            $stringToHash,
            hex2bin($key)
        ));

        //? 4. build payment URL
        $url = "https://npg.moamalat.net:6006/lightbox?" . http_build_query([
            'MID' => $mID,
            'TID' => $tID,
            'Amount' => $amount . "000",
            'MerchantReference' => $merchantRef,
            'TrxDateTime' => $dt,
            'SecureHash' => $secureHash,
        ]);

        return $url;
    }
}