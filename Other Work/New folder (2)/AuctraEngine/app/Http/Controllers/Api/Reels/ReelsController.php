<?php
namespace App\Http\Controllers\Api\Reels;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reels\CreateRequest;
use App\Http\Requests\Reels\UpdateRequest;
use App\Http\Resources\ReelResource;
use App\Repositories\Interfaces\ReelsRepositoryInterface;
use Illuminate\Http\Request;

class ReelsController extends Controller
{
    public function __construct(protected ReelsRepositoryInterface $reelsRepo)
    {
    }

    //? For You Feed (MAIN API) persantage 80/20 for reels interest & recommended
    public function feed()
    {
        return successResponse('success', ReelResource::collection($this->reelsRepo->recommendedReels(5)), 200);
    }

    //? All reels (explore)
    public function index()
    {
        $reels = $this->reelsRepo->allReels();
        return successResponse('success', ReelResource::collection($reels), 200);
    }

    //? Single reel
    public function show(int $id)
    {
        $reel = $this->reelsRepo->showReel($id);

        return successResponse('success', new ReelResource($reel), 200);
    }

    // ? create 
    public function create(CreateRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $reel = $this->reelsRepo->createReel($data);
        return successResponse('success', new ReelResource($reel), 200);
    }

    // ? update
    public function update(UpdateRequest $request, int $id)
    {
        $reel = $this->reelsRepo->updateReel($id, $request->validated());
        return successResponse('success', new ReelResource($reel), 200 );
    }
    // ! delete
    public function delete(int $id)
    {
        return successResponse('success', $this->reelsRepo->deleteReel($id), 200);
    }

    //? User reels
    public function userReels(int $userId)
    {
        $reels = $this->reelsRepo->userReels($userId);

        return successResponse('success', ReelResource::collection($reels), 200);
    }

    //? My reels
    public function myReels()
    {
        $reels = $this->reelsRepo->myReels();

        return successResponse('success', ReelResource::collection($reels), 200);
    }

    //? search
    public function search(Request $request)
    {
        $request->validate(['query' => 'required']);
        $reels = $this->reelsRepo->searchReels($request->get('query'));
        return successResponse('success', ReelResource::collection($reels), 200);
    }
}