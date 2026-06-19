<?php

namespace App\Contracts;
interface PayableInterface

{
    public function getPrice(): float;
    public function getPaymentType();
    public function getDescription(): string;
}