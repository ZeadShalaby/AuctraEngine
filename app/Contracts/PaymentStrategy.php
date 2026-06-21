<?php
namespace App\Contracts;

interface PaymentStrategy
{
    /**
     * @param $user
     * @param $payable 
     * @param $price 
     */
    public function pay($user, $payable, $price ,$type = null);
}