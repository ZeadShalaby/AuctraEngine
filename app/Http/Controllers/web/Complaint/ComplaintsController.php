<?php

namespace App\Http\Controllers\web\Complaint;

use App\DataTables\ComplaintsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComplaintsController extends Controller
{
    //
    public function index(ComplaintsDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('complaints.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
