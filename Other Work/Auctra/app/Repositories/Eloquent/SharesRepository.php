<?php

namespace App\Repositories\Eloquent;

use App\Models\Share;
use App\Repositories\Interfaces\SharesRepositoryInterface;

class SharesRepository implements SharesRepositoryInterface
{

    public function __construct(protected Share $share)
    {
    }


    public function my($perPage = 15)
    {
        return $this->share->where('user_id', auth()->user()->id)->paginate($perPage);
    }


    public function toggle(array $data)
    {
        return toggleInteraction(Share::class, [
            'shareable_id' => $data['shareable_id'],
            'shareable_type' => $data['shareable_type'],
        ], 'share');
    }
}