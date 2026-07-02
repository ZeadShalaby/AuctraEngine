<?php

namespace App\Http\Controllers\web\Auctions;

use App\DataTables\AuctionWatcherCardDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuctionWatcherController extends Controller
{
    //
    public function index(AuctionWatcherCardDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('auctionwatcher.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
