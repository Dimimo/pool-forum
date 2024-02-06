<?php

namespace Dimimo\PoolForum\Models\Discussion;

use Illuminate\Database\Eloquent\Model;

/**
 * Dimimo\PoolForum\Models\Discussion\User
 *
 * @property int $id
 * @property int $user_id
 * @property int $discussion_id
 * @property string|null $last_read_at
 * @property int|null $last_read_post_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDiscussionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastReadPostNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserId($value)
 *
 * @mixin \Eloquent
 */
class User extends Model
{
    protected $fillable = [
        'discussion_id', 'user_id', 'last_read_at', 'last_read_post_number',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('pool-forum.table_names.discussion_users', 'discussion_user'));
    }
}
