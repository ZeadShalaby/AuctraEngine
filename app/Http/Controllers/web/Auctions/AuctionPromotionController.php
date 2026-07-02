<?php

namespace App\Http\Controllers\web\Auctions;

use App\DataTables\AuctionPromotionDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuctionPromotionController extends Controller
{
    //
    public function index(AuctionPromotionDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('auctionpromotions.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
