<?php

namespace App\DataTables;

use App\Models\RechargeCard;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RechargeCardDataTable extends DataTable
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
            ->editColumn('card_name', fn($row) => $row->card_name)

            ->editColumn('card_number', function ($row) {
                return '<span class="fw-semibold">' . $row->card_number . '</span>';
            })

            ->editColumn('recharge_amount', function ($row) {
                return '<span class="fw-bold text-success">'
                    . number_format($row->recharge_amount, 2)
                    . '</span>';
            })

            ->editColumn('used', function ($row) {
                return $row->used
                    ? '<span class="badge bg-success">Used</span>'
                    : '<span class="badge bg-warning">Unused</span>';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i')
                    . '<br><small class="text-muted">'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'recharges', ['show']);
            })

            ->filterColumn('card_name', function ($query, $keyword) {
                $query->where('cards.name', 'like', "%{$keyword}%");
            })

            ->rawColumns([
                'card_number',
                'recharge_amount',
                'used',
                'created_at',
                'action'
            ])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\RechargeCard $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RechargeCard $model): QueryBuilder
    {
        $rechargeCards = $model->newQuery()
            ->join('cards', 'cards.id', '=', 'recharge_cards.card_id')
            ->select([
                'recharge_cards.*',
                'cards.name as card_name',
            ]);

        return filterByDateRange($rechargeCards, 'recharge_cards.created_at');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('rechargecard-table')
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

            Column::make('card_name')
                ->title('Card')
                ->searchable(true),

            Column::make('card_number')
                ->title('Card Number'),

            Column::make('recharge_amount')
                ->title('Amount'),

            Column::make('used')
                ->title('Status'),

            Column::make('created_at')
                ->name('recharge_cards.created_at')
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
        return 'RechargeCard_' . date('YmdHis');
    }
}
