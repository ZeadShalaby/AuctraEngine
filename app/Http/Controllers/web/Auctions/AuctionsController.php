<?php

namespace App\Http\Controllers\web\Auctions;

use App\DataTables\AuctionsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuctionsController extends Controller
{
    //

    public function index(AuctionsDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('auctions.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
