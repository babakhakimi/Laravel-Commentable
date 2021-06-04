<?php

declare(strict_types=1);

/**
 * Laravel Commentable Package by Babak Hakimi.
 */

namespace BabakHakimi\LaravelCommentable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Kalnoy\Nestedset\NodeTrait;

class Comment extends Model
{
    use NodeTrait;

    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * @return mixed
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    /**
     * @param Model $commentable
     * @param $data
     * @param Model $creator
     *
     * @return static
     */
    public function createComment(Model $commentable, $data, Model $creator): self
    {
        return $commentable->comments()->create(array_merge($data, [
            'creator_id'   => $creator->getAuthIdentifier(),
            'creator_type' => $creator->getMorphClass(),
        ]));
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function updateComment($id, $data): bool
    {
        return (bool) static::find($id)->update($data);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function deleteComment($id): bool
    {
        return (bool) static::find($id)->delete();
    }
    

    /**
     * approve a comment
     */
    public function approve()
    {
        if ($this->update(['approved' => true])) {
            return true;
        }
        return false;
    }
    

    /**
     * unapprove a comment
     */
    public function unapprove()
    {
        if ($this->update(['approved' => false])) {
            return true;
        }
        return false;
    }

    public function scopeOwner($query)
    {
        return $query->where('creator_id', Auth::id());
    }

    public function scopeRootComments($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeApproved($query)
    {
        if(Auth::check()){
            return $query->where(function($q) {
                $q->where('approved', true)
                    ->orWhere('creator_id', Auth::id());
            });
        }
        return $query->where('approved', true);


    }

    
}
