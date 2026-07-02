<?php

namespace App\Http\Controllers\web\Cards;

use App\DataTables\RechargeCardDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RechargeController extends Controller
{
    //
    public function index(RechargeCardDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('recharges.title')]);
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('recharges.create') . '" class="btn btn-sm btn-primary" role="button">Add Card</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets', 'headerAction'));
    }
}
