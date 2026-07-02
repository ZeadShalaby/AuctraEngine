<?php

namespace App\DataTables;

use App\Enums\PromotionStatus;
use App\Models\AuctionPromotion;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AuctionPromotionDataTable extends DataTable
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

            ->editColumn('price', function ($row) {
                return '<span class="fw-bold text-success" style="font-size:13px">'
                    . number_format($row->price, 2)
                    . '</span>';
            })

            ->editColumn('status', function ($row) {
                return match ($row->status) {
                    PromotionStatus::PENDING->value => '<span class="badge bg-warning">' . ucfirst($row->status) . '</span>',
                    PromotionStatus::ACTIVE->value => '<span class="badge bg-success">' . ucfirst($row->status) . '</span>',
                    PromotionStatus::EXPIRED->value => '<span class="badge bg-secondary">' . ucfirst($row->status) . '</span>',
                    PromotionStatus::CANCELLED->value => '<span class="badge bg-danger">' . ucfirst($row->status) . '</span>',
                    PromotionStatus::REVIEW->value => '<span class="badge bg-info">' . ucfirst($row->status) . '</span>',
                    default => '<span class="badge bg-dark">' . ucfirst($row->status) . '</span>',
                };
            })

            ->editColumn('starts_at', function ($row) {
                return $row->starts_at->format('Y-m-d H:i')
                    . '<br><small class="text-primary">'
                    . $row->starts_at->diffForHumans()
                    . '</small>';
            })

            ->editColumn('expires_at', function ($row) {
                return $row->expires_at->format('Y-m-d H:i')
                    . '<br><small class="text-danger">'
                    . $row->expires_at->diffForHumans()
                    . '</small>';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d')
                    . '<br><small>'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->filterColumn('auction_title', function ($query, $keyword) {
                $query->where('auctions.title', 'like', "%{$keyword}%");
            })

            ->filterColumn('package_name', function ($query, $keyword) {
                $query->where('promotion_packages.name', 'like', "%{$keyword}%");
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'promotions', ['show', 'delete']);
            })

            ->rawColumns([
                'price',
                'status',
                'starts_at',
                'expires_at',
                'created_at',
                'action'
            ])

            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AuctionPromotion $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(AuctionPromotion $model): QueryBuilder
    {
        $promotions = $model->newQuery()
            ->join('auctions', 'auctions.id', '=', 'auction_promotions.auction_id')
            ->join(
                'promotion_packages',
                'promotion_packages.id',
                '=',
                'auction_promotions.promotion_package_id'
            )
            ->select([
                'auction_promotions.*',
                'auctions.title as auction_title',
                'promotion_packages.name as package_name',
            ]);

        return filterByDateRange($promotions, 'auction_promotions.created_at');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('auctionpromotion-table')
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

            Column::make('auction_title')
                ->title('Auction'),

            Column::make('package_name')
                ->title('Package'),

            Column::make('price')
                ->title('Price'),

            Column::make('status')
                ->title('Status'),

            Column::make('starts_at')
                ->title('Starts At'),

            Column::make('expires_at')
                ->title('Expires At'),

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
        return 'AuctionPromotion_' . date('YmdHis');
    }
}
