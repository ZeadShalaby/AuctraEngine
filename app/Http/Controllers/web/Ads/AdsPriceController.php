<?php

namespace App\Http\Controllers\web\Ads;

use App\DataTables\AdsPriceDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdsPriceController extends Controller
{
    //
    public function index(AdsPriceDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('adsprice.title')]);
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('price.create') . '" class="btn btn-sm btn-primary" role="button">Add Price</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets', 'headerAction'));
    }
}
