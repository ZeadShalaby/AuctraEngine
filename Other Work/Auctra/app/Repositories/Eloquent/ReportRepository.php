<?php

namespace App\Repositories\Eloquent;

use App\Models\reports\Report;
use App\Repositories\Interfaces\ReportRepositoryInterface;

class ReportRepository implements ReportRepositoryInterface
{
    public function __construct(protected Report $report) {}

    public function all($perPage = 15)
    {
        return $this->report->latest()->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->report->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->report->create($data);
    }

    public function update(int $id, array $data)
    {
        $report = $this->find($id);
        $report->update($data);

        return $report;
    }

    public function delete(int $id)
    {
        return $this->find($id)->delete();
    }

    public function getByUser(int $userId)
    {
        return $this->report
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function getByStatus(string $status)
    {
        return $this->report
            ->where('status', $status)
            ->latest()
            ->get();
    }

    public function assignAdmin(int $reportId, int $adminId)
    {
        $report = $this->find($reportId);

        $report->update([
            'admin_id' => $adminId,
        ]);

        return $report;
    }

    public function addAction(int $reportId, array $actionData)
    {
        $report = $this->find($reportId);

        return $report->actions()->create($actionData);
    }
}