<?php

namespace App\Http\Controllers\web\Companys;

use App\DataTables\CompanyDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    //
    public function index(CompanyDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('companies.title')]);
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('companys.create') . '" class="btn btn-sm btn-primary" role="button">Add Sub Category</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets', 'headerAction'));
    }
}
