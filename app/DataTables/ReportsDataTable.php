<?php

namespace App\DataTables;

use App\Enums\ReportStatus;
use App\Models\reports\Report;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ReportsDataTable extends DataTable
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

            ->editColumn('id', fn($row) => "#{$row->id}")

            ->editColumn('reporter_name', fn($row) => $row->reporter_name)

            ->editColumn('reportable_type', function ($row) {
                return class_basename($row->reportable_type);
            })

            ->editColumn('status', function ($row) {
                return match ($row->status) {
                    ReportStatus::PENDING->value => '<span class="badge bg-warning">' . $row->status . '</span>',
                    ReportStatus::REVIEWED->value => '<span class="badge bg-info">' . $row->status . '</span>',
                    ReportStatus::RESOLVED->value => '<span class="badge bg-success">' . $row->status . '</span>',
                    ReportStatus::REJECTED->value => '<span class="badge bg-danger">' . $row->status . '</span>',
                    default => '<span class="badge bg-secondary">' . $row->status . '</span>',
                };
            })

            ->editColumn('admin_name', function ($row) {
                return $row->admin_name
                    ?: '<span class="text-muted">Not Assigned</span>';
            })

            ->editColumn('description', function ($row) {
                return str($row->description)->limit(80);
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i')
                    . '<br><small class="text-muted">'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'reports', ['show']);
            })

            ->filterColumn('reporter_name', function ($query, $keyword) {
                $query->whereRaw(
                    "CONCAT(reporters.first_name,' ',reporters.last_name) LIKE ?",
                    ["%{$keyword}%"]
                );
            })

            ->filterColumn('admin_name', function ($query, $keyword) {
                $query->whereRaw(
                    "CONCAT(admins.first_name,' ',admins.last_name) LIKE ?",
                    ["%{$keyword}%"]
                );
            })

            ->rawColumns([
                'status',
                'admin_name',
                'created_at',
                'action'
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Report $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Report $model): QueryBuilder
    {
        $reports = $model->newQuery()
            ->leftJoin('users as reporters', 'reporters.id', '=', 'reports.user_id')
            ->leftJoin('users as admins', 'admins.id', '=', 'reports.admin_id')
            ->select([
                'reports.*',
                DB::raw("CONCAT(reporters.first_name, ' ', reporters.last_name) as reporter_name"),
                DB::raw("CONCAT(admins.first_name, ' ', admins.last_name) as admin_name"),
            ]);

        return filterByDateRange($reports, 'reports.created_at');

    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('reports-table')
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

            Column::make('id')->title('ID'),

            Column::make('reporter_name')
                ->title('Reporter'),

            Column::make('reportable_type')
                ->title('Type'),

            Column::make('reportable_id')
                ->title('Target ID'),

            Column::make('description')
                ->title('Description'),

            Column::make('status')
                ->title('Status'),

            Column::make('admin_name')
                ->title('Reviewed By'),

            Column::make('created_at')
                ->name('reports.created_at')
                ->title('Created At'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Reports_' . date('YmdHis');
    }
}
