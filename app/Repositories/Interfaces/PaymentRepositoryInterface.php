<?php

namespace App\Repositories\Interfaces;

interface PaymentRepositoryInterface
{
    public function callback(string $merchantRef, array $getway_details);

    public function chargeWallet($type = null, $card = null);

    public function balance();

    public function walletLog($start = null, $end = null);

}