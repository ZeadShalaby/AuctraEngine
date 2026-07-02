<?php

namespace App\Http\Controllers\web\Notifications;

use App\DataTables\NotificationsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //
    public function index(NotificationsDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('notifications.title')]);
        $assets = ['data-table'];
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets'));
    }
}
