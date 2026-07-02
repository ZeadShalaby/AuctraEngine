<?php

namespace App\Http\Controllers\web\Wallet;

use App\DataTables\PaymentsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index(PaymentsDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('payments.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
