<?php

namespace App\DataTables;

use App\Enums\PromotionType;
use App\Models\PromotionPackage;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PromotionPackageDataTable extends DataTable
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

            ->editColumn('name', function ($row) {

                $name = e($row->name);

                return "
                <span data-bs-toggle='tooltip'
                      title='{$name}'>
                    " . Str::limit($name, 30) . "
                </span>";
            })


            ->editColumn('type', function ($row) {
                return match ($row->type) {
                    PromotionType::FEATURED->value => "<span class='badge bg-success'>{$row->type}</span>",
                    PromotionType::PROMOTED->value => "<span class='badge bg-warning'>{$row->type}</span>",
                    default => "<span class='badge bg-secondary'>{$row->type}</span>",
                };
            })

            ->editColumn('price', function ($row) {

                $price = number_format($row->price, 2);

                return "<span class='text-success fw-bold'>{$price}</span>";
            })

            ->editColumn('days', function ($row) {

                return "<span class='badge bg-primary'>{$row->days} Days</span>";
            })

            ->editColumn('auction_count', function ($row) {

                return "<span class='badge bg-dark'>{$row->auctionPromotions->count()}</span>";
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
                return actionColumn($row, 'packages');
            })

            ->filterColumn('duration', function ($query, $keyword) {
                $query->where('days', 'like', "%{$keyword}%");
            })

            ->rawColumns([
                'name',
                'price',
                'type',
                'days',
                'auction_count',
                'is_active',
                'created_at',
                'action',
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\PromotionPackage $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PromotionPackage $model): QueryBuilder
    {
        return filterByDateRange(
            $model->newQuery()->with('auctionPromotions'),
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
            ->setTableId('promotionpackage-table')
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

            Column::make('id'),

            Column::make('name'),

            Column::make('type')->title('Type'),

            Column::make('days')->title('Duration')->orderable(true),

            Column::make('price'),

            Column::make('auction_count')
                ->title('Auctions')
                ->searchable(false)
                ->orderable(false),
            Column::make('is_active')->title('Status'),

            Column::make('created_at'),

            Column::computed('action')->exportable(false)->printable(false)->width(70)->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'PromotionPackage_' . date('YmdHis');
    }
}
