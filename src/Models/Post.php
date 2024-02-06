<?php

namespace Dimimo\PoolForum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Dimimo\PoolForum\Models\Post
 *
 * @property int $id
 * @property int $discussion_id
 * @property int|null $number
 * @property int|null $user_id
 * @property string|null $content
 * @property string|null $edited_at
 * @property int|null $edited_user_id
 * @property string|null $hidden_at
 * @property int|null $hidden_user_id
 * @property string|null $ip_address
 * @property int $is_private
 * @property int $is_approved
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Dimimo\PoolForum\Models\Discussion|null $discussion
 * @property-read \App\Models\User|null $editor
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDiscussionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereEditedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereHiddenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereHiddenUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Post withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'discussion_id', 'number', 'user_id', 'content', 'edited_at', 'edited_user_id',
        'hidden_at', 'hidden_user_id', 'ip_address', 'is_private', 'is_approved',
    ];

    protected $dates = [
        'hidden_at',
        'edited_at',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('pool-forum.table_names.posts', 'posts'));
    }

    public function user()
    {
        return $this->belongsTo(config('pool-forum.models.user', 'App\User'), 'user_id', 'id');
    }

    public function discussion()
    {
        return $this->belongsTo('\Dimimo\PoolForum\Models\Discussion', 'discussion_id', 'id');
    }

    public function editor()
    {
        return $this->belongsTo(config('pool-forum.models.user', 'App\User'), 'edited_user_id', 'id');
    }

    /**
     * Determines if some user can edit current Post.
     *
     * @param  int|string  $userId  if not defined it takes current user automatically from
     *                              Auth facade
     * @return bool
     */
    public function canEdit($userId = null)
    {
        if (! $userId && Auth::user()) {
            $userId = Auth::user()->id;
        }

        if (is_string($userId)) {
            $user = intval($userId);
        }

        // If user is post/discussion owner returns true, otherwise false.
        return $this->user_id === $userId; // || $this->discussion->canEdit($userId);
    }
}
