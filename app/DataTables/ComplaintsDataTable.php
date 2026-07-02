<?php

namespace App\DataTables;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ComplaintsDataTable extends DataTable
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

            ->editColumn('user_name', fn($row) => $row->user_name ?: '-')

            ->editColumn('description', function ($row) {
                $description = e($row->description);
                return '
                        <span
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="' . $description . '">
                            ' . Str::limit($description, 20, '...') . '
                        </span>';
            })

            ->editColumn('complaintable_type', function ($row) {
                return $row->complaintable_type
                    ? class_basename($row->complaintable_type)
                    : '-';
            })

            ->editColumn('status', function ($row) {

                return match ($row->status) {
                    ComplaintStatus::PENDING->value => '<span class="badge bg-warning">' . ucfirst($row->status) . '</span>',
                    ComplaintStatus::REVIEWED->value => '<span class="badge bg-info">' . ucfirst($row->status) . '</span>',
                    ComplaintStatus::RESOLVED->value => '<span class="badge bg-success">' . ucfirst($row->status) . '</span>',
                    ComplaintStatus::REJECTED->value => '<span class="badge bg-danger">' . ucfirst($row->status) . '</span>',
                    default => '<span class="badge bg-secondary">' . ucfirst($row->status) . '</span>',
                };
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d')
                    . '<br><small>'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->filterColumn('user_name', function ($query, $keyword) {
                $query->whereRaw(
                    "CONCAT(users.first_name,' ',users.last_name) LIKE ?",
                    ["%{$keyword}%"]
                );
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'complaints', ['show']);
            })

            ->rawColumns([
                'status',
                'user_name',
                'description',
                'created_at',
                'action',
            ])

            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Complaint $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Complaint $model): QueryBuilder
    {
        $complaints = $model->newQuery()
            ->leftJoin('users', 'users.id', '=', 'complaints.user_id')
            ->select([
                'complaints.*',
                DB::raw("CONCAT(users.first_name,' ',users.last_name) as user_name"),
            ]);

        return filterByDateRange($complaints, 'complaints.created_at');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('complaints-table')
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

            Column::make('user_name')
                ->title('User'),

            Column::make('name')
                ->title('Name'),

            Column::make('email')
                ->title('Email'),

            Column::make('complaintable_type')
                ->title('Type'),

            Column::make('description')
                ->title('Description')
                ->orderable(false),

            Column::make('status')
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
        return 'Complaints_' . date('YmdHis');
    }
}
