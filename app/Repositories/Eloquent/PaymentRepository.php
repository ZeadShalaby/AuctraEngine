<?php

namespace App\Repositories\Eloquent;

use App\Enums\PaymentGateway;
use App\Enums\PaymentType;
use App\Models\Wallet\Wallet;
use App\Models\Wallet\WalletLog;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Services\PaymentManager;
use Illuminate\Support\Facades\DB;

class PaymentRepository implements PaymentRepositoryInterface
{

    public function __construct(protected PaymentManager $paymentManager, protected Wallet $wallet, protected WalletLog $walletLog)
    {
    }
    public function callback(string $merchantRef, array $gateway_details)
    {
        return completeCallback($merchantRef, $gateway_details);
    }

    public function chargeWallet($type = null, $card = null)
    {
        $wallet = $this->wallet::firstOrCreate(['user_id' => auth()->id()]);
        return DB::transaction(function () use ($wallet, $type, $card) {
            $balance = 0;
            if ($type == PaymentGateway::CARD->value) {
                $balance = checkAvailableCard(auth()->user(), $card);
            }
            $payment = $this->paymentManager->handlePayment($type, auth()->user(), $wallet, $balance, PaymentType::WALLET_DEPOSIT->value);
            return $payment;
        });
    }

    public function balance()
    {
        $wallet = $this->wallet::firstOrCreate(['user_id' => auth()->id()]);
        return $wallet;
    }

    public function walletLog($start = null, $end = null)
    {
        $query = $this->walletLog;
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }
        return $query->latest()->paginate(15);
    }

}