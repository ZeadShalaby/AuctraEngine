<?php

namespace App\DataTables;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class NotificationsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('id', function ($row) {
                $id = e($row->id);
                return '
                        <span
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="' . $id . '">
                            ' . Str::limit($id, 20, '...') . '
                        </span>';
            })
            ->editColumn('type', function ($row) {
                return class_basename($row->type);
            })

            ->addColumn('title', function ($row) {
                return data_get($row->data, 'title', '-');
            })

            ->addColumn('message', function ($row) {
                return data_get($row->data, 'message', '-');
            })

            ->editColumn('read_at', function ($row) {
                return $row->read_at
                    ? '<span class="badge bg-success">Read</span>'
                    : '<span class="badge bg-warning">Unread</span>';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i')
                    . '<br><small>'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->editColumn('data', function ($row) {
                return collect($row->data)
                    ->map(fn($value, $key) => "<strong>{$key}:</strong> " . (is_array($value) ? json_encode($value) : $value))
                    ->implode('<br>');
            })


            ->rawColumns(['data', 'read_at'])

            ->filterColumn('message', function ($query, $keyword) {
                $query->whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(data,'$.message')) LIKE ?",
                    ["%{$keyword}%"]
                );
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'notifications', ['show']);
            })

            ->rawColumns([
                'read_at',
                'data',
                'id',
                'created_at',
                'action',
            ])

            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Notification $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): QueryBuilder
    {
        $adminIds = User::where('user_type', 'admin')
            ->pluck('id');

        $notifications = DatabaseNotification::query()
            ->where('notifiable_type', 'user')
            ->whereIn('notifiable_id', $adminIds);

        return filterByDateRange(
            $notifications,
            'notifications.created_at'
        );
    }
    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('notifications-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->buttons([
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [

            Column::make('id')
                ->title('ID'),

            Column::make('data'),

            Column::make('message')
                ->title('Message')
                ->orderable(false),

            Column::make('type')
                ->title('Type'),

            Column::make('read_at')
                ->title('Status'),

            Column::make('created_at')
                ->title('Created At'),

            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false)
                ->width(80)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Notifications_' . date('YmdHis');
    }
}
