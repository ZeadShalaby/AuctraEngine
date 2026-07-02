<?php

namespace App\DataTables;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CompanyDataTable extends DataTable
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

            ->addColumn('image', function ($row) {
                return '<img src="' . $row->getFirstMediaUrl('companyLogo') . '"
                        width="45"
                        height="45"
                        class="rounded"
                        style="object-fit:cover">';
            })

            ->editColumn('status', function ($row) {
                return $row->status == 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })

            
            ->editColumn('phone', function ($row) {

                $phoneRaw =  $row->phone;

                $phone = preg_replace('/[^0-9]/', '', $phoneRaw);

                $waUrl = "https://wa.me/{$phone}";

                return '
                        <a href="' . $waUrl . '" target="_blank"
                        class="text-success text-decoration-none d-inline-flex align-items-center gap-2">

                            <i class="fab fa-whatsapp fa-lg"></i>

                        </a>
                        <span>' . $row->country_code . ' ' .$phoneRaw . '</span>
                    ';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y/m/d')
                    . '<br><small>'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'companys');
            })

            ->rawColumns([
                'image',
                'status',
                'phone',
                'created_at',
                'action'
            ])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Company $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Company $model): QueryBuilder
    {
        $companies = $model->newQuery();

        return filterByDateRange(
            $companies,
            'companies.created_at'
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
            ->setTableId('company-table')
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

            Column::computed('image')
                ->title('Logo')
                ->searchable(false)
                ->orderable(false),

            Column::make('name')
                ->title('Name'),

            Column::make('code')
                ->title('Code'),

            Column::make('email')
                ->title('Email'),

            Column::make('phone')
                ->title('Phone'),

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
        return 'Company_' . date('YmdHis');
    }
}
