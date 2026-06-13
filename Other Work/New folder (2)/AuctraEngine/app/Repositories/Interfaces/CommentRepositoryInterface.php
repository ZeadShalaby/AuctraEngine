<?php

namespace App\Repositories\Interfaces;

interface CommentRepositoryInterface
{
    public function getCommentsByCommentable($commentableId, $commentableType, $perPage = 10);
    public function create(array $data);
    public function show(int $id);
    public function update(array$data, int $id);
    public function destroy(int $id);
}