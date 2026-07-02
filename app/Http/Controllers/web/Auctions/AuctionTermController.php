<?php

namespace App\Http\Controllers\web\Auctions;

use App\DataTables\AuctionTermsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuctionTermController extends Controller
{
    //
    public function index(AuctionTermsDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('auctionterms.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
