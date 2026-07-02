<?php

namespace App\DataTables;

use App\Models\Bid;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BidsDataTable extends DataTable
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

            ->editColumn('amount', function ($row) {
                return '<span class="fw-bold text-success" style="font-size:13px">'
                    . number_format($row->amount, 2)
                    . ' SAR</span>';
            })

            ->editColumn('max_auto_bid', function ($row) {
                return $row->max_auto_bid
                    ? '<span class="fw-bold text-primary" style="font-size:13px">'
                    . number_format($row->max_auto_bid, 2)
                    . ' SAR</span>'
                    : '-';
            })

            ->editColumn('is_auto', function ($row) {
                return $row->is_auto
                    ? '<span class="badge bg-success">Yes</span>'
                    : '<span class="badge bg-secondary">No</span>';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y/m/d')
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

            ->filterColumn('auction_title', function ($query, $keyword) {
                $query->where('auctions.title', 'like', "%{$keyword}%");
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'bids', ['show']);
            })

            ->rawColumns([
                'amount',
                'max_auto_bid',
                'is_auto',
                'created_at',
                'action'
            ])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Bid $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Bid $model): QueryBuilder
    {
        $bids = $model->newQuery()
            ->join('users', 'users.id', '=', 'bids.user_id')
            ->join('auctions', 'auctions.id', '=', 'bids.auction_id')
            ->select([
                'bids.*',
                DB::raw("CONCAT(users.first_name,' ',users.last_name) as user_name"),
                'auctions.title as auction_title',
            ]);

        return filterByDateRange($bids, 'bids.created_at');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('bids-table')
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

            Column::make('user_name')
                ->title('User'),

            Column::make('amount')
                ->title('Bid Amount'),

            Column::make('is_auto')
                ->title('Auto Bid'),

            Column::make('max_auto_bid')
                ->title('Max Auto Bid'),

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
        return 'Bids_' . date('YmdHis');
    }
}
