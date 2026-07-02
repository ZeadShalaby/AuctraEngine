<?php

namespace App\Http\Controllers\web\Auctions;

use App\DataTables\PromotionPackageDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PromotionPackageController extends Controller
{
    //

    public function index(PromotionPackageDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('PromotionPackage.title')]);
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('packages.create') . '" class="btn btn-sm btn-primary" role="button">Add Price</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets', 'headerAction'));
    }
}
