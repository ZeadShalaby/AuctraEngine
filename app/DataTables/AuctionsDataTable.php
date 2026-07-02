<?php

namespace App\DataTables;

use App\Enums\AuctionCondition;
use App\Enums\AuctionStatus;
use App\Models\Auction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AuctionsDataTable extends DataTable
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

            ->editColumn('title', function ($row) {
                $title = e($row->title);
                return '
                        <span
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="' . $title . '">
                            ' . Str::limit($title, 25, '...') . '
                        </span>';
            })

            ->editColumn('description', function ($row) {
                $description = e($row->description);
                return '
                        <span
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="' . $description . '">
                            ' . Str::limit($description, 40, '...') . '
                        </span>';
            })

            ->addColumn('user_name', function ($row) {
                return $row->user?->full_name ?? '-';
            })

            ->addColumn('winner_name', function ($row) {
                return $row->winner?->full_name ?? '-';
            })

            ->addColumn('category_name', function ($row) {
                return $row->category?->name ?? '-';
            })

            ->addColumn('sub_category_name', function ($row) {
                return $row->subCategory?->name ?? '-';
            })

            ->editColumn('start_price', function ($row) {

                $price = number_format($row->start_price, 2);

                return "<span class='text-success fw-bold'>{$price}</span>";
            })

            ->editColumn('current_price', function ($row) {

                if (!$row->current_price) {
                    return "-";
                }

                $price = number_format($row->current_price, 2);

                return "<span class='text-primary fw-bold'>{$price}</span>";
            })

            ->editColumn('buy_now_price', function ($row) {

                if (!$row->buy_now_price) {
                    return "-";
                }

                $price = number_format($row->buy_now_price, 2);

                return "<span class='text-warning fw-bold'>{$price}</span>";
            })

            ->editColumn('terms_price', function ($row) {

                return "<span class='text-info fw-bold'>"
                    . number_format($row->terms_price, 2)
                    . "</span>";
            })

            ->editColumn('views', function ($row) {

                return "<span class='badge bg-secondary'>{$row->views}</span>";
            })

            ->editColumn('bids_count', function ($row) {

                return "<span class='badge bg-dark'>{$row->bids_count}</span>";
            })

            ->editColumn('condition', function ($row) {
                return match ($row->condition) {
                    AuctionCondition::NEW ->value => "<span class='badge bg-success'>{$row->condition}</span>",
                    AuctionCondition::USED->value => "<span class='badge bg-warning'>{$row->condition}</span>",
                    default => "<span class='badge bg-secondary'>{$row->condition}</span>",
                };
            })

            ->editColumn('status', function ($row) {


                return match ($row->status) {

                    AuctionStatus::PENDING->value =>
                    "<span class='badge bg-warning'>Pending</span>",

                    AuctionStatus::PROCESSING->value =>
                    "<span class='badge bg-primary'>Processing</span>",

                    AuctionStatus::ACTIVE->value =>
                    "<span class='badge bg-success'>Active</span>",

                    AuctionStatus::ENDED->value =>
                    "<span class='badge bg-dark'>Ended</span>",

                    AuctionStatus::CANCELLED->value =>
                    "<span class='badge bg-danger'>Cancelled</span>",

                    default =>
                    "<span class='badge bg-secondary'>{$row->status}</span>",
                };
            })

            ->editColumn('start_at', function ($row) {

                if (!$row->start_at) {
                    return '-';
                }

                return $row->start_at->format('Y-m-d H:i')
                    . '<br><small class="text-muted">'
                    . $row->start_at->diffForHumans()
                    . '</small>';
            })

            ->editColumn('end_at', function ($row) {

                if (!$row->end_at) {
                    return '-';
                }

                return $row->end_at->format('Y-m-d H:i')
                    . '<br><small class="text-muted">'
                    . $row->end_at->diffForHumans()
                    . '</small>';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d')
                    . '<br><small>'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->filterColumn('user_name', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('winner_name', function ($query, $keyword) {
                $query->whereHas('winner', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('category_name', function ($query, $keyword) {
                $query->whereHas('category', function ($q) use ($keyword) {
                    $q->where('name_en', 'like', "%{$keyword}%")
                        ->orWhere('name_ar', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('sub_category_name', function ($query, $keyword) {
                $query->whereHas('subCategory', function ($q) use ($keyword) {
                    $q->where('name_en', 'like', "%{$keyword}%")
                        ->orWhere('name_ar', 'like', "%{$keyword}%");

                });
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'auctions', ['show', 'delete']);
            })


            ->rawColumns([
                'title',
                'description',
                'user_name',
                'winner_name',
                'category_name',
                'sub_category_name',
                'start_price',
                'current_price',
                'buy_now_price',
                'terms_price',
                'views',
                'bids_count',
                'condition',
                'status',
                'start_at',
                'end_at',
                'created_at',
                'action',
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Auction $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Auction $model): QueryBuilder
    {
        $query = $model->newQuery()->with('user', 'winner', 'category', 'subCategory');
        return filterByDateRange(
            $query,
            ['start_at', 'end_at']
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
            ->setTableId('auctions-table')
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

            Column::make('title'),

            Column::make('description'),

            Column::make('user_name')->title('User'),

            Column::make('winner_name')->title('Winner'),

            Column::make('category_name')->title('Category'),

            Column::make('sub_category_name')->title('Sub Category'),

            Column::make('start_price')->title('Start Price'),

            Column::make('current_price')->title('Current Price'),

            Column::make('buy_now_price')->title('Buy Now'),

            Column::make('terms_price')->title('Terms'),

            Column::make('views'),

            Column::make('bids_count')->title('Bids'),

            Column::make('condition'),

            Column::make('status'),

            Column::make('start_at')->title('Start'),

            Column::make('end_at')->title('End'),

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
        return 'Auctions_' . date('YmdHis');
    }
}
