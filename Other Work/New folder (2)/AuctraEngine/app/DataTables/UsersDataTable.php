<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build DataTable class.
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)

            // /* =========================
            //  * Country
            //  * ========================= */
            // ->editColumn('userProfile.country', function ($user) {
            //     return $user->userProfile->country ?? '-';
            // })

            // /* =========================
            //  * Company
            //  * ========================= */
            // ->editColumn('userProfile.company_name', function ($user) {
            //     return $user->userProfile->company_name ?? '-';
            // })

            ->editColumn('phone_number', function ($user) {

                $phoneRaw = $user->phone_number;

                // تنظيف الرقم
                $phone = preg_replace('/[^0-9]/', '', $phoneRaw);

                $waUrl = "https://wa.me/{$phone}";

                return '
                        <a href="' . $waUrl . '" target="_blank"
                        class="text-success text-decoration-none d-inline-flex align-items-center gap-2">

                            <i class="fab fa-whatsapp fa-lg"></i>

                        </a>
                        <span>' . $phoneRaw . '</span>
                    ';
            })
            /* =========================
             * Status Switch
             * ========================= */
            ->addColumn('status_switch', function ($user) {

                $checked = $user->status === 'active' ? 'checked' : '';

                $color = match ($user->status) {
                    'active' => 'success',
                    'inactive' => 'danger',
                    'banned' => 'dark',
                    default => 'secondary'
                };

                return '
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-status"
                            type="checkbox"
                            data-id="' . $user->id . '"
                            ' . $checked . '>
                             <span class="badge status-badge bg-' . $color . '" data-id="' . $user->id . '">
                        ' . $user->status . '
                    </span>
                    </div>

                    
                ';
            })

            /* =========================
             * Notifications Switch
             * ========================= */
            ->addColumn('notifications', function ($user) {

                $checked = $user->notifications_enabled ? 'checked' : '';
                $color = $user->notifications_enabled ? 'success' : 'danger';
                $text = $user->notifications_enabled ? 'Enabled' : 'Disabled';

                return '
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input toggle-notification"
                                type="checkbox"
                                data-id="' . $user->id . '"
                                ' . $checked . '>
                        </div>

                        <span class="badge notification-badge bg-' . $color . '"
                            data-id="' . $user->id . '">
                            ' . $text . '
                        </span>
                    </div>
                ';
            })

            /* =========================
             * Email Switch
             * ========================= */
            ->addColumn('email_toggle', function ($user) {

                $checked = $user->email_enabled ? 'checked' : '';
                $color = $user->email_enabled ? 'success' : 'danger';
                $text = $user->email_enabled ? 'Enabled' : 'Disabled';

                return '
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-email"
                            type="checkbox"
                            data-id="' . $user->id . '"
                            ' . $checked . '>

                     <span class="badge email-badge bg-' . $color . '"
                            data-id="' . $user->id . '">
                            ' . $text . '
                        </span>
                    </div>
                ';
            })



            /* =========================
             * Ads enabled Switch
             * ========================= */
            ->addColumn('ads_enabled', function ($user) {

                $checked = $user->ads_enabled ? 'checked' : '';
                $color = $user->ads_enabled ? 'success' : 'danger';
                $text = $user->ads_enabled ? 'Enabled' : 'Disabled';

                return '
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-ads"
                            type="checkbox"
                            data-id="' . $user->id . '"
                            ' . $checked . '>
                      <span class="badge ads-badge bg-' . $color . '"
                            data-id="' . $user->id . '">
                            ' . $text . '
                        </span>
                    </div>
                ';
            })

            /* =========================
             * Auction enabled Switch
             * ========================= */
            ->addColumn('auction_enabled', function ($user) {

                $checked = $user->auction_enabled ? 'checked' : '';
                $color = $user->auction_enabled ? 'success' : 'danger';
                $text = $user->auction_enabled ? 'Enabled' : 'Disabled';

                return '
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-auction"
                            type="checkbox"
                            data-id="' . $user->id . '"
                            ' . $checked . '>
                      <span class="badge auction-badge bg-' . $color . '"
                            data-id="' . $user->id . '">
                            ' . $text . '
                        </span>
                    </div>
                ';
            })

            /* =========================
             * Created At
             * ========================= */
            ->editColumn('created_at', function ($user) {
                return $user->created_at->format('Y/m/d');
            })

            /* =========================
             * Full Name Search
             * ========================= */
            ->filterColumn('full_name', function ($query, $keyword) {
                $sql = "CONCAT(users.first_name,' ',users.last_name) like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })

            /* =========================
             * Company Filter
             * ========================= */
            ->filterColumn('userProfile.company_name', function ($query, $keyword) {
                $query->whereHas('userProfile', function ($q) use ($keyword) {
                    $q->where('company_name', 'like', "%{$keyword}%");
                });
            })

            /* =========================
             * Country Filter
             * ========================= */
            ->filterColumn('userProfile.country', function ($query, $keyword) {
                $query->whereHas('userProfile', function ($q) use ($keyword) {
                    $q->where('country', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('action', 'users.action')

            ->rawColumns([
                'phone_number',
                'status_switch',
                'notifications',
                'email_toggle',
                'ads_enabled',
                'auction_enabled',
                'action'
            ]);
    }

    /**
     * Query source
     */
    public function query()
    {
        return User::query()
            ->whereNotIn('user_type', ['admin', 'demo_admin'])
            ->with('userProfile');
    }

    /**
     * HTML Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                "processing" => true,
                "autoWidth" => false,
            ]);
    }

    /**
     * Columns
     */
    protected function getColumns()
    {
        return [

            ['data' => 'id', 'name' => 'id', 'title' => 'ID'],

            [
                'data' => 'full_name',
                'name' => 'full_name',
                'title' => 'Full Name',
                'orderable' => false
            ],

            ['data' => 'phone_number', 'name' => 'phone_number', 'title' => 'Phone'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],

            // [
            //     'data' => 'userProfile.country',
            //     'name' => 'userProfile.country',
            //     'title' => 'Country'
            // ],

            // ['data' => 'userProfile.company_name', 'name' => 'userProfile.company_name', 'title' => 'Company'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Join Date'],
            [
                'data' => 'status_switch',
                'name' => 'status',
                'title' => 'Change Status',
                'orderable' => false,
                'searchable' => false
            ],

            [
                'data' => 'notifications',
                'name' => 'notifications_enabled',
                'title' => 'Notifications',
                'orderable' => false,
                'searchable' => false
            ],

            [
                'data' => 'email_toggle',
                'name' => 'email_enabled',
                'title' => 'Email',
                'orderable' => false,
                'searchable' => false
            ],

            [
                'data' => 'ads_enabled',
                'name' => 'ads_enabled',
                'title' => 'Ads',
                'orderable' => false,
                'searchable' => false
            ],

            [
                'data' => 'auction_enabled',
                'name' => 'auction_enabled',
                'title' => 'Auction',
                'orderable' => false,
                'searchable' => false
            ],




            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->width(80)
                ->addClass('text-center'),
        ];
    }
}