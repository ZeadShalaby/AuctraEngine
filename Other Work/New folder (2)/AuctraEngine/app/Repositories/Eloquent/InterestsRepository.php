<?php

namespace App\Repositories\Eloquent;

use App\Models\Interest;
use App\Models\UserInterest;
use App\Repositories\Interfaces\InterestsRepositoryInterface;


class InterestsRepository implements InterestsRepositoryInterface
{
    public function __construct(protected UserInterest $interest){}

    public function my($perPage = 10)
    {
        $interests = auth()->user()->interests()->with('category')->paginate($perPage);
        return $interests;
    }

    public function toggle(int $id): bool
    {
        $interest = Interest::firstOrCreate([
            'category_id' => $id
        ]);
        $interest->keywords = extractKeywords($interest->category->slug . ' ' . $interest->category->description);
        $interest->save();
        if ($interest->users()->where('user_id', auth()->id())->exists()) {
            $interest->users()->detach(auth()->id());
            return false;
        }

        $interest->users()->attach(auth()->id(), [
            'score' => 0
        ]);

        return true;
    }





}