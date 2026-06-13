<?php
namespace App\Repositories\Interfaces;

interface InterestsRepositoryInterface
{

    public function my($perPage = 10);
    public function toggle(int $id);

}