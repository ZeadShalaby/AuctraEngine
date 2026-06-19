<?php

namespace App\Repositories\Eloquent;

use App\Models\Wallet\Payment;
use App\Models\Wallet\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{

    public function __construct(protected Transaction $transaction){}

    public function my($perPage = 15, $status = null)
    {
        return $this->transaction::where('user_id', auth()->user()->id)->when($status, fn($q) => $q->where('status', $status))->with('user', 'source')->latest()->paginate($perPage);
    }
    public function show(int $id)
    {
        $transaction = $this->transaction::with('user', 'source')->findOrFail($id);
        checkOwner(auth()->id(), $transaction->user_id);
        return $transaction;
    }


}