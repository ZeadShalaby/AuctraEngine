<?php

namespace App\Repositories\Eloquent;

use App\Events\InteractionToggled;
use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;

class CommentRepository implements CommentRepositoryInterface
{
    public function __construct(protected Comment $comment){}
    
    public function getCommentsByCommentable($commentableId,$commentableType, $perPage = 10)
    {
        return $this->comment::where('commentable_id', $commentableId)
            ->where('commentable_type', $commentableType)
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        $comment = $this->comment::create($data);
        event(new InteractionToggled($data['commentable'],'increment','comment'));
        return $comment;
    }

    public function show(int $id)
    {
        return $this->comment::findOrFail($id);
    }

    public function update(array $data,int $id)
    {
        $comment = $this->comment::findOrFail($id);
        return $comment->update($data);
    }

    public function destroy(int $id)
    {
        $comment = $this->comment::findOrFail($id);
        $commentable = $comment->commentable;
        $comment->delete();
        event(new InteractionToggled($commentable,'decrement','comment'));
        return true;
    }
}