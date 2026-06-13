<?php

namespace App\Repositories\Eloquent;

use App\Models\Interest;
use App\Repositories\Interfaces\InterestsRepositoryInterface;
use Exception;
use Override;

class InterestsRepository implements InterestsRepositoryInterface
{
    public function __construct(protected Interest $interest)
    {
    }

    public function my($perPage = 10)
    {
        return $this->interest->where('user_id', auth()->user()->id)->paginate($perPage = 10);
    }

    public function toggle($id)
    {
        return toggleInteraction(Interest::class, [
            'category_id' => $id,
        ]);
    }




}