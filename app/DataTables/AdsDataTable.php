<?php

namespace App\DataTables;

use App\Enums\AdsFeedType;
use App\Enums\AdsStatus;
use App\Models\Ad;
use App\Models\Ads;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AdsDataTable extends DataTable
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

            ->editColumn('title', function ($row) {

                $title = e($row->title);
                return '
                        <span
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="' . $title . '">
                            ' . \Illuminate\Support\Str::limit($title, 25, '...') . '
                        </span>';
            })

            ->editColumn('description', function ($row) {
                $description = e($row->description);
                return '
                        <span
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="' . $description . '">
                            ' . \Illuminate\Support\Str::limit($description, 40, '...') . '
                        </span>';
            })

            ->editColumn('feed_type', function ($row) {
                return match ($row->feed_type) {
                    AdsFeedType::POSTS->value => "<span class='badge bg-success'>{$row->feed_type}</span>",
                    AdsFeedType::REELS->value => "<span class='badge bg-warning'>{$row->feed_type}</span>",
                    default => "<span class='badge bg-secondary'>{$row->feed_type}</span>",
                };
            })

            ->editColumn('starts_at', function ($row) {

                if (!$row->starts_at) {
                    return '-';
                }

                return $row->starts_at->format('Y-m-d H:i')
                    . '<br><small class="text-muted">'
                    . $row->starts_at->diffForHumans()
                    . '</small>';
            })

            ->editColumn('expires_at', function ($row) {

                if (!$row->expires_at) {
                    return '-';
                }

                return $row->expires_at->format('Y-m-d H:i')
                    . '<br><small class="text-muted">'
                    . $row->expires_at->diffForHumans()
                    . '</small>';
            })

            ->editColumn('current_impressions', function ($row) {

                return "<span class='badge bg-primary'>{$row->current_impressions}</span>";
            })

            ->editColumn('max_impressions', function ($row) {

                return "<span class='badge bg-dark'>{$row->max_impressions}</span>";
            })

            ->editColumn('link_url', function ($row) {

                if (!$row->link_url) {
                    return '-';
                }

                return '
                        <a href="' . $row->link_url . '"
                        target="_blank"
                        class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i> View
                        </a>';
            })

            ->editColumn('adPrice.price', function ($row) {

                return match ($row->status) {
                    AdsStatus::ACTIVE->value => "<span class='badge bg-success'>{$row->adPrice->price}</span>",
                    AdsStatus::ENDED->value => "<span class='badge bg-danger'>{$row->adPrice->price}</span>",
                    AdsStatus::PENDING->value => "<span class='badge bg-warning'>{$row->adPrice->price}</span>",
                    default => "<span class='badge bg-secondary'>{$row->adPrice->price}</span>",
                };
            })

            ->editColumn('status', function ($row) {

                return match ($row->status) {
                    AdsStatus::ACTIVE->value => "<span class='badge bg-success'>{$row->status}</span>",
                    AdsStatus::ENDED->value => "<span class='badge bg-info'>{$row->status}</span>",
                    AdsStatus::PENDING->value => "<span class='badge bg-warning'>{$row->status}</span>",
                    AdsStatus::REJECTED->value => "<span class='badge bg-danger'>{$row->status}</span>",
                    AdsStatus::REVIEW->value => "<span class='badge bg-primary'>{$row->status}</span>",
                    default => "<span class='badge bg-secondary'>{$row->status}</span>",
                };
            })

            ->filterColumn('user_name', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y/m/d')
                    . '<br><small>'
                    . $row->created_at->diffForHumans()
                    . '</small>';
            })

            ->addColumn('action', function ($row) {
                return actionColumn($row, 'ads', ['show']);
            })
            ->rawColumns([
                'status',
                'feed_type',
                'adPrice.price',
                'current_impressions',
                'max_impressions',
                'link_url',
                'title',
                'description',
                'starts_at',
                'expires_at',
                'created_at',
                'action',
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Ad $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Ads $model): QueryBuilder
    {
        $ads = $model->newQuery()->with('user', 'adable', 'adPrice');
        return filterByDateRange(
            $ads,
            ['starts_at', 'expires_at']
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
            ->setTableId('ads-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                "processing" => true,
                "autoWidth" => false,
            ])
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->orderBy(0, 'desc')
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

            Column::make('user_name')->title('User'),

            Column::make('title'),

            Column::make('description'),

            Column::make('feed_type')->title('Feed'),

            Column::make('adPrice.price')->title('Price'),

            Column::make('current_impressions')->title('Current'),

            Column::make('max_impressions')->title('Max'),


            Column::make('link_url')->title('Link'),

            Column::make('starts_at')->title('Start'),

            Column::make('expires_at')->title('Expire'),

            Column::make('status'),

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
        return 'Ads_' . date('YmdHis');
    }
}
