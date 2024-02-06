<?php

namespace Dimimo\PoolForum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Dimimo\PoolForum\Models\Discussion\User as DiscussionUser;

/**
 * Dimimo\PoolForum\Models\Discussion
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property int $comment_count
 * @property int $participant_count
 * @property int $post_number_index
 * @property int|null $user_id
 * @property int|null $first_post_id
 * @property string|null $last_posted_at
 * @property int|null $last_posted_user_id
 * @property int|null $last_post_id
 * @property int $is_private
 * @property int $is_approved
 * @property int $is_locked
 * @property int $is_sticky
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Dimimo\PoolForum\Models\Post|null $lastPost
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Dimimo\PoolForum\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Dimimo\PoolForum\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereCommentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereFirstPostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereIsLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereIsSticky($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereLastPostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereLastPostedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereLastPostedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereParticipantCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion wherePostNumberIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Discussion withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Discussion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'comment_count', 'participant_count', 'post_number_index',
        'user_id', 'first_post_id', 'last_posted_at', 'last_posted_user_id', 'last_post_id',
        'is_private', 'is_approved', 'is_locked', 'is_sticky',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('pool-forum.table_names.discussions', 'discussions'));
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany('\Dimimo\PoolForum\Models\Tag', config('pool-forum.table_names.discussion_tags', 'discussion_tag'))->withPivot('tag_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('pool-forum.models.user', 'App\Models\User'), 'user_id', 'id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany('\Dimimo\PoolForum\Models\Post', 'discussion_id', 'id');
    }

    public function lastPost(): HasOne
    {
        return $this->hasOne('\Dimimo\PoolForum\Models\Post', 'discussion_id', 'id')->orderBy('created_at', 'desc');
    }

    /**
     * Determines if some user has read some post.
     *
     * @param  null  $userId
     */
    public function isRead($userId = null): bool
    {
        if (! $userId && Auth::user()) {
            $userId = Auth::user()->id;
        }
        if (is_string($userId)) {
            $userId = intval($userId);
        }
        $read = DiscussionUser::where('user_id', $userId)
            ->where('discussion_id', $this->id)
            ->first();

        return $read && $this->post_number_index === $read->last_read_post_number;
    }

    /**
     * Determines if some user can edit current discussion.
     *
     * @param  int|string|null  $userId  if not defined it takes current user automatically from
     *                                   Auth facade
     */
    public function canEdit(int|string|null $userId = null): bool
    {
        if (! $userId && Auth::user()) {
            $userId = Auth::user()->id;
        }
        if (is_string($userId)) {
            $userId = intval($userId);
        }

        return $this->user_id === $userId;
    }
}
