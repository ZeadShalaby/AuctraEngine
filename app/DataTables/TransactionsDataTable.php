<?php

namespace App\DataTables;

use App\Enums\PaymentStatus;
use App\Models\Wallet\Transaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TransactionsDataTable extends DataTable
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
            ->addColumn('user_name', function ($row) {
                return $row->user?->full_name;
            })
            ->addColumn('source_name', function ($row) {
                return class_basename($row->source_type);
            })
            ->addColumn('description', function ($row) {
                return __("wallet.payments.for : ") . class_basename($row->description);
            })
            ->editColumn('amount', function ($row) {

                return match ($row->status) {
                    PaymentStatus::COMPLETED->value => "<span class='badge bg-success'>{$row->amount}</span>",
                    PaymentStatus::FAILED->value => "<span class='badge bg-danger'>{$row->amount}</span>",
                    PaymentStatus::PENDING->value => "<span class='badge bg-warning'>{$row->amount}</span>",
                    default => "<span class='badge bg-secondary'>{$row->amount}</span>",
                };
            })

            ->editColumn('status', function ($row) {

                return match ($row->status) {
                    PaymentStatus::COMPLETED->value => "<span class='badge bg-success'>{$row->status}</span>",
                    PaymentStatus::FAILED->value => "<span class='badge bg-danger'>{$row->status}</span>",
                    PaymentStatus::PENDING->value => "<span class='badge bg-warning'>{$row->status}</span>",
                    default => "<span class='badge bg-secondary'>{$row->status}</span>",
                };
            })

            ->editColumn('created_at', function ($query) {
                return $query->created_at->format('Y/m/d');
            })

            ->filterColumn('user_name', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('source_name', function ($query, $keyword) {
                $query->where('source_type', 'like', "%{$keyword}%");
            })

            ->filterColumn('description', function ($query, $keyword) {
                $query->where('description', 'like', "%\\{$keyword}");
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'payments', ['show']);
            })

            ->rawColumns([
                'status',
                'amount',
                'created_at',
                'action'
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Transaction $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model): QueryBuilder
    {
        return $model->newQuery()->with('user', 'source');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('transactions-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                "processing" => true,
                "autoWidth" => false,
            ])
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


            Column::computed('user_name')
                ->title('User')
                ->orderable(false),

            Column::make('amount')
                ->title('Amount')
                ->orderable(true),

            Column::make('status')
                ->title('Status')
                ->orderable(true),

            Column::make('type')
                ->title('Type')
                ->orderable(true),

            Column::make('source_name')
                ->title('Source')
                ->orderable(true),

            Column::make('description')
                ->title('Description')
                ->orderable(true),

            Column::make('created_at')
                ->title('Created Date'),

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
        return 'Transactions_' . date('YmdHis');
    }
}
