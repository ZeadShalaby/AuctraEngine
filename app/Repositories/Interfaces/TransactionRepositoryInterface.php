<?php

namespace App\Repositories\Interfaces;

interface TransactionRepositoryInterface
{

    public function show(int $id);
    public function my($perPage = 15, $status = null);


}