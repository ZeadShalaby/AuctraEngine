<?php

namespace App\Http\Controllers\web\Wallet;

use App\DataTables\TransactionsDataTable;
use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{

    public function index(TransactionsDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('transactions.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
