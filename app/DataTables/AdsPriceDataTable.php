<?php

namespace App\DataTables;

use App\Enums\AdsFeedType;
use App\Models\AdPrice;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AdsPriceDataTable extends DataTable
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

            ->editColumn('placement', function ($row) {

                return match ($row->placement) {
                    AdsFeedType::POSTS->value => "<span class='badge bg-success'>{$row->placement}</span>",
                    AdsFeedType::REELS->value => "<span class='badge bg-warning'>{$row->placement}</span>",
                    default => "<span class='badge bg-secondary'>{$row->placement}</span>",
                };

            })
            ->editColumn('price', function ($row) {

                $price = number_format($row->price, 2);

                return "<span class='text-primary fw-bold fs-6'>{$price}</span>";
            })

            ->editColumn('max_impressions', function ($row) {
                return "<span class='badge bg-dark'>{$row->max_impressions}</span>";
            })

            ->editColumn('max_days', function ($row) {
                return "<span class='badge bg-secondary'>{$row->max_days} Days</span>";
            })

            ->editColumn('is_active', function ($row) {

                return $row->is_active
                    ? "<span class='badge bg-success'>Active</span>"
                    : "<span class='badge bg-danger'>Inactive</span>";
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d')
                    . '<br><small>'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'price');
            })

            ->rawColumns([
                'placement',
                'price',
                'max_impressions',
                'max_days',
                'is_active',
                'created_at',
                'action',
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AdsPrice $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(AdPrice $model): QueryBuilder
    {
        return filterByDateRange(
            $model->newQuery(),
            'created_at'
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
            ->setTableId('adsprice-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                "processing" => true,
                "autoWidth" => false,
            ])
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

            Column::make('id'),

            Column::make('placement'),

            Column::make('price'),

            Column::make('max_impressions')->title('Max Impressions'),

            Column::make('max_days')->title('Max Days'),

            Column::make('is_active')->title('Status'),

            Column::make('created_at'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(70)
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
        return 'AdsPrice_' . date('YmdHis');
    }
}
