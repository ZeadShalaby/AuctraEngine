<?php

namespace App\Http\Controllers\web\Category;

use App\DataTables\SubCategoryDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubCategoriesController extends Controller
{
    //
    public function index(SubCategoryDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('subcategories.title')]);
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('subcategories.create') . '" class="btn btn-sm btn-primary" role="button">Add Sub Category</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets', 'headerAction'));
    }
}
