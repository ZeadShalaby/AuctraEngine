<?php

namespace App\Http\Controllers\web\Category;

use App\Http\Controllers\Controller;
use App\DataTables\CategoryDataTable;
class CategoriesController extends Controller
{
    public function index(CategoryDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('categories.title')]);
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('categories.create') . '" class="btn btn-sm btn-primary" role="button">Add Category</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets', 'headerAction'));
    }
}
