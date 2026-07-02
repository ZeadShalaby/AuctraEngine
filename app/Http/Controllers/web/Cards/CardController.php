<?php

namespace App\Http\Controllers\web\Cards;

use App\DataTables\CardDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CardController extends Controller
{
    //
    public function index(CardDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('cards.title')]);
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('cards.create') . '" class="btn btn-sm btn-primary" role="button">Add Card</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets', 'headerAction'));
    }
}
