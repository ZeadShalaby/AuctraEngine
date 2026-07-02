<?php

namespace App\DataTables;

use App\Models\reports\ReportAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ReportActionDataTable extends DataTable
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

            ->editColumn('report_number', fn($row) => '#' . $row->report_number)

            ->editColumn('admin_name', fn($row) => $row->admin_name)

            ->editColumn('action', function ($row) {
                return '<span class="badge bg-primary">'
                    . str($row->action)->replace('_', ' ')->title()
                    . '</span>';
            })

            ->editColumn('notes', function ($row) {
                return $row->notes ?: '<span class="text-muted">No Notes</span>';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i')
                    . '<br><small class="text-muted">'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->addColumn('action_btn', function ($row) {
                return actionColumn($row, 'actions', ['show']);
            })

            ->filterColumn('admin_name', function ($query, $keyword) {
                $query->whereRaw(
                    "CONCAT(admins.first_name, ' ', admins.last_name) LIKE ?",
                    ["%{$keyword}%"]
                );
            })
            ->orderColumn('admin_name', function ($query, $order) {
                $query->orderByRaw(
                    "CONCAT(admins.first_name, ' ', admins.last_name) {$order}"
                );
            })

            ->rawColumns([
                'action',
                'notes',
                'created_at',
                'action_btn',
            ])

            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ReportAction $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ReportAction $model): QueryBuilder
    {
        $reportActions = $model->newQuery()->with('report', 'user')
            ->join('reports', 'reports.id', '=', 'report_actions.report_id')
            ->join('users as admins', 'admins.id', '=', 'report_actions.admin_id')
            ->select([
                'report_actions.*',
                'reports.id as report_number',
                DB::raw("CONCAT(admins.first_name, ' ', admins.last_name) as admin_name"),
            ]);

        return filterByDateRange($reportActions, 'report_actions.created_at');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('reportaction-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
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
            Column::make('id')->title('ID'),

            Column::make('report_number')
                ->name('reports.id')
                ->title('Report'),

            Column::make('admin_name')
                ->name('admin_name')
                ->title('Admin'),

            Column::make('action')
                ->title('Action'),

            Column::make('notes')
                ->title('Notes'),

            Column::make('created_at')
                ->name('report_actions.created_at')
                ->title('Created At'),

            Column::computed('action_btn')
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
        return 'ReportAction_' . date('YmdHis');
    }
}
