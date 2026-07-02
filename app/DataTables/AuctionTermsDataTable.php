<?php

namespace App\DataTables;

use App\Models\AuctionTerm;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AuctionTermsDataTable extends DataTable
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
                return '<span class="text-success fw-bold small">'
                    . number_format($row->amount, 2)
                    . '</span>';
            })
            
            ->editColumn('is_refunded', function ($row) {
                return $row->is_refunded
                    ? '<span class="badge bg-success">Refunded</span>'
                    : '<span class="badge bg-danger">Not Refunded</span>';
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
                return actionColumn($row, 'terms', ['show']);
            })

            ->rawColumns(['amount', 'created_at', 'is_refunded', 'action'])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AuctionTerm $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(AuctionTerm $model): QueryBuilder
    {
        $terms = $model->newQuery()
            ->join('users', 'users.id', '=', 'auction_terms.user_id')
            ->join('auctions', 'auctions.id', '=', 'auction_terms.auction_id')
            ->select([
                'auction_terms.*',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as user_name"),
                'auctions.title as auction_title',
            ]);

        return filterByDateRange($terms, 'auction_terms.created_at');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('auctionterms-table')
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

            Column::make('id')
                ->title('ID'),

            Column::make('auction_title')
                ->title('Auction'),

            Column::make('user_name')
                ->title('User'),

            Column::make('amount')
                ->title('Amount'),

            Column::make('is_refunded')
                ->title('Refund'),

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
        return 'AuctionTerms_' . date('YmdHis');
    }
}
