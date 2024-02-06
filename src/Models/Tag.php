<?php

namespace Dimimo\PoolForum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Dimimo\PoolForum\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $color
 * @property string|null $background_color
 * @property int $discussion_count
 * @property string|null $last_posted_at
 * @property int|null $last_posted_discussion_id
 * @property int|null $last_posted_user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereDiscussionCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereLastPostedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereLastPostedDiscussionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereLastPostedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Tag extends Model
{
    use SoftDeletes;

    protected $table = 'forum_tags';

    protected $fillable = [
        'name', 'slug', 'description', 'color', 'background_color',
        'discussion_count', 'last_posted_at', 'last_posted_discussion_id',
        'last_posted_user_id',
    ];
}
