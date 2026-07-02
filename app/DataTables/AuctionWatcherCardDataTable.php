<?php

namespace App\DataTables;

use App\Models\AuctionWatcher;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AuctionWatcherCardDataTable extends DataTable
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
            ->editColumn('user_name', fn($row) => $row->user_name)
            ->editColumn('auction_title', fn($row) => $row->auction_title)
            ->addColumn('action', function ($row) {
                return actionColumn($row, 'watchers', ['show']);
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

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y/m/d') .
                    '<br><small style="color: gray">' . $row->created_at->diffForHumans() . '</small>';
            })
            ->rawColumns(['created_at', 'action'])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AuctionWatcherCard $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(AuctionWatcher $model): QueryBuilder
    {
        $watchers = $model->newQuery()->with('user', 'auction')
            ->join('users', 'users.id', '=', 'auction_watchers.user_id')
            ->join('auctions', 'auctions.id', '=', 'auction_watchers.auction_id')
            ->select([
                'auction_watchers.*',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as user_name"),
                'auctions.title as auction_title',
            ]);
        return filterByDateRange(
            $watchers,
            'auction_watchers.created_at'
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
            ->setTableId('auctionwatchercard-table')
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
                ->title('Auction')
                ->searchable(true),

            Column::make('user_name')
                ->title('User')
                ->searchable(true),

            Column::make('created_at')
                ->title('Watched At'),

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
        return 'AuctionWatcherCard_' . date('YmdHis');
    }
}
