<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function __construct(protected TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function myTransactions($status = null)
    {
        $transactions = $this->transactionRepository->my($status);
        return successResponse(_('messages.success'), TransactionResource::collection($transactions), 200);
    }

    public function show(int $id)
    {
        return successResponse(_('messages.success'), TransactionResource::make($this->transactionRepository->show($id)), 200);
    }
}
