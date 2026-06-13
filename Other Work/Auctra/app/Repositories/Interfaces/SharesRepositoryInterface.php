<?php

namespace App\Repositories\Interfaces;

interface SharesRepositoryInterface
{
    public function my($perPage = 15);
    public function toggle(array $data);
}