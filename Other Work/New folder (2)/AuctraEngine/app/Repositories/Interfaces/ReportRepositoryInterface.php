<?php 

namespace App\Repositories\Interfaces;

interface ReportRepositoryInterface
{
    public function all();

    public function find(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function getByUser(int $userId);

    public function getByStatus(string $status);

    public function assignAdmin(int $reportId, int $adminId);

    public function addAction(int $reportId, array $actionData);
}