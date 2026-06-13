<?php

namespace App\Http\Controllers\Api\Comments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Commments\CommentRequest;
use App\Http\Requests\Commments\CreateRequest;
use App\Http\Requests\Commments\UpdateRequest;
use App\Http\Resources\CommentResource;
use App\Repositories\Interfaces\CommentRepositoryInterface;

class CommentsController extends Controller
{
    public function __construct(protected CommentRepositoryInterface $commentRepository){}
    public function index(CommentRequest $request)
    {
        $data = $request->validated();
        $comments = $this->commentRepository->getCommentsByCommentable($data['commentable_type'], $data['commentable_id'], );
        return successResponse(__('messages.retrieved_successfully'), CommentResource::collection($comments), 200);
    }

    public function store(CreateRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $comment = CommentResource::make($this->commentRepository->create($data));
        $comment->unsetRelation('commentable');
        return successResponse(__('messages.created_successfully'), $comment, 201);
    }

    public function show($id)
    {
        return successResponse(__('messages.retrieved_successfully'), CommentResource::make($this->commentRepository->show($id)), 200);
    }

    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $this->commentRepository->update($data, $id);
        return successResponse(__('messages.updated_successfully'), CommentResource::make($this->commentRepository->show($id)), 200);
    }

    public function destroy(int $id)
    {
        return successResponse(__('messages.deleted_successfully'), $this->commentRepository->destroy($id), 200);
    }
}
