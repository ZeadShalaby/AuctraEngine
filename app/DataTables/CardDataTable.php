<?php

namespace App\DataTables;

use App\Models\Card;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CardDataTable extends DataTable
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

            ->editColumn('selling_price', function ($row) {
                return '<span class="fw-bold text-success" style="font-size:13px">'
                    . number_format($row->selling_price, 2)
                    . ' SAR</span>';
            })

            ->editColumn('amount', function ($row) {
                return '<span class="fw-bold text-primary" style="font-size:13px">'
                    . number_format($row->amount, 2)
                    . ' SAR</span>';
            })

            ->editColumn('recharge_amount', function ($row) {
                return '<span class="fw-bold text-info" style="font-size:13px">'
                    . number_format($row->recharge_amount, 2)
                    . ' SAR</span>';
            })

            ->editColumn('status', function ($row) {
                return $row->status == 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })

            ->editColumn('type', function ($row) {
                return ucfirst($row->type);
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y/m/d')
                    . '<br><small>'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->filterColumn('company_name', function ($query, $keyword) {
                $query->where('companies.name', 'like', "%{$keyword}%");
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'cards');
            })

            ->rawColumns([
                'selling_price',
                'amount',
                'recharge_amount',
                'status',
                'created_at',
                'action'
            ])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Card $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Card $model): QueryBuilder
    {
        $cards = $model->newQuery()
            ->join('companies', 'companies.id', '=', 'cards.company_id')
            ->select([
                'cards.*',
                'companies.name as company_name',
            ]);

        return filterByDateRange($cards, 'cards.created_at');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('card-table')
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

            Column::make('name')
                ->title('Card'),

            Column::make('company_name')
                ->title('Company'),

            Column::make('type')
                ->title('Type'),

            Column::make('selling_price')
                ->title('Selling Price'),

            Column::make('amount')
                ->title('Amount'),

            Column::make('recharge_amount')
                ->title('Recharge'),

            Column::make('status')
                ->title('Status'),

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
        return 'Card_' . date('YmdHis');
    }
}
