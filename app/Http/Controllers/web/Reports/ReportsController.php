<?php

namespace App\Http\Controllers\web\Reports;

use App\DataTables\ReportsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    //
    public function index(ReportsDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('reports.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
