<?php

namespace App\Http\Controllers\web\Ads;

use App\DataTables\AdsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    //
    public function index(AdsDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('ads.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
