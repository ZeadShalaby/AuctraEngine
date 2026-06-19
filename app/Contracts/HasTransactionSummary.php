<?php

namespace App\Contracts;

interface HasTransactionSummary
{
    public function transactionSummary(): array;
}