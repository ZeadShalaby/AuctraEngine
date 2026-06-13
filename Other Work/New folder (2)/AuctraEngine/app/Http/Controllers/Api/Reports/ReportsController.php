<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\CreateRequest;
use App\Repositories\Eloquent\ReportRepository;

class ReportsController extends Controller
{
    //
    public function __construct(protected ReportRepository $reportRepo){}

    public function store(CreateRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $report = $this->reportRepo->create($data);
        return successResponse(__('messages.created'), $report, 200);
    }


}
