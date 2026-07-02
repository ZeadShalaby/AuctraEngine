<?php

namespace App\Http\Controllers\web\Bids;

use App\DataTables\BidsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BidsController extends Controller
{
    //
    public function index(BidsDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('bids.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
