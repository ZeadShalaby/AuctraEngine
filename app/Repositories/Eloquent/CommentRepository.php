<?php

namespace App\Repositories\Eloquent;

use App\Events\InteractionToggled;
use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\Relation;

class CommentRepository implements CommentRepositoryInterface
{
    public function __construct(protected Comment $comment)
    {
    }

    public function getCommentsByCommentable($commentableType, $commentableId, $perPage = 10)
    {
        return $this->comment
            ->where('commentable_id', $commentableId)
            ->where('commentable_type', $commentableType)
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        $comment = $this->comment::create($data);
        $modelClass = Relation::getMorphedModel($data['commentable_type']);
        $parent = $modelClass::find($data['commentable_id']);
        event(new InteractionToggled($parent, 'increment', 'comment'));
        return $comment->load('user', 'commentable');
    }

    public function show(int $id)
    {
        return $this->comment::with('user')->findOrFail($id);
    }

    public function update(array $data, int $id)
    {
        $comment = $this->comment::findOrFail($id);
        $comment->update([
            'content' => $data['content'] ?? $comment->content,
        ]);
        return $comment->load('user');
    }

    public function destroy(int $id)
    {
        $comment = $this->comment::findOrFail($id);
        $commentable = $comment->commentable;
        $comment->delete();
        event(new InteractionToggled($commentable, 'decrement', 'comment'));
        return true;
    }
}