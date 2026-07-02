<?php

namespace App\Http\Controllers\web\Reports;

use App\DataTables\ReportActionDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsActionController extends Controller
{
    //
    public function index(ReportActionDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('actions.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
