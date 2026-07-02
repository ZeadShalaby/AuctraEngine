<?php

namespace App\DataTables;

use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SubCategoryDataTable extends DataTable
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
                $image = $row->getFirstMediaUrl('image');
                return '<img src="' . ($image ?: asset('images/no-image.png')) . '" 
                width="50"
                height="50"
                style="object-fit:cover;border-radius:8px;">';
            })

            ->addColumn('name', function ($row) {
                return app()->getLocale() == 'ar'
                    ? $row->name_ar
                    : $row->name_en;
            })

            ->addColumn('category_name', function ($row) {
                return app()->getLocale() == 'ar'
                    ? $row->category?->name_ar
                    : $row->category?->name_en;
            })
            ->filterColumn('category_name', function ($query, $keyword) {
                $query->whereHas('category', function ($q) use ($keyword) {
                    $q->where('name_ar', 'like', "%{$keyword}%")
                        ->orWhere('name_en', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('created_at', function ($query) {
                return $query->created_at->format('Y/m/d')
                    . '<br><small>'
                    . $query->created_at->diffForHumans()
                    . '</small>';
            })
            ->addColumn('action', function ($row) {
                return actionColumn($row, 'subcategories');
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name_ar', 'like', "%{$keyword}%")
                        ->orWhere('name_en', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns([
                'created_at',
                'action',
                'image'
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\SubCategory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SubCategory $model): QueryBuilder
    {
        return $model->newQuery()->with('category');
        ;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('subcategory-table')
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
            Column::make('id')
                ->title('ID'),

            Column::make('image')
                ->title('Image')
                ->orderable(false)
                ->exportable(false),

            Column::make('name')
                ->name('name_en') // أو name_ar
                ->title('Name')
                ->orderable(false),

            Column::computed('category_name')
                ->title('Category')
                ->orderable(false),

            Column::make('slug')
                ->title('Slug')
                ->orderable(false),

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
        return 'SubCategory_' . date('YmdHis');
    }
}
