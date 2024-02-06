<?php

use Dimimo\PoolForum\Models\Discussion;
use Dimimo\PoolForum\Models\Discussion\User as DiscussionUser;
use Carbon\Carbon;
use Illuminate\Support\Str;

// If discussion has not defined directly, we can find it
// with slug var.
if ( ! isset($back_url))
{
    $back_url = null;
}

if ( ! isset($discussion))
{
    $discussion = Discussion::query()->where('slug', $slug)->first();
    // If doesn't exist, we create it
    if ( ! $discussion)
    {
        //If no config, we set common config.
        if ( ! isset($config) || is_array($config))
        {
            $config = [
                'is_private'  => 1,
                'is_approved' => 1,
                'is_sticky'   => 0,
                'is_locked'   => 0,
            ];
        }
        // If no user_id, we set current user as owner
        if ( ! isset($config['user_id']))
        {
            $config['user_id'] = Auth::user()->id;
        }
        // If no title, we revert slug to human words
        if ( ! isset($config['title']))
        {
            $config['title'] = str_replace('-', ' ', $slug);
        }
        // Sets slug
        $config['slug'] = $slug;
        // Now we can create the discussion
        $discussion = Discussion::query()->create($config);
    }
}

//Show all posts if user is owner, otherwise, hide unproved posts
$posts = ($discussion->canEdit()) ? $discussion->posts()->orderBy('created_at', 'ASC')->get() :
    $discussion->posts()->where('is_approved', 1)->orderBy('created_at', 'ASC')->get();


if (Auth::check())
{
    //Store current user "read"
    $discussionUser = DiscussionUser::query()->where('user_id', Auth::user()->id)->where('discussion_id', $discussion->id)->first();
    if ( ! $discussionUser)
    {
        $discussionUser = new DiscussionUser();
        $discussionUser->fill([
                                  'discussion_id' => $discussion->id,
                                  'user_id'       => Auth::user()->id,
                              ]);
    }
    $discussionUser->last_read_at          = Carbon::now()->toDateTimeString();
    $discussionUser->last_read_post_number = $discussion->post_number_index;
    $discussionUser->save();
}
?>
<div class="">
    <div class="mt-2 flex">
        <div>
            <h1 class="capitalize text-primary-600 text-lg hidden">{{$discussion->title}}</h1>
            @foreach($discussion->tags as $tag)
                <span class="py-1 px-2 rounded-full" style="color:{{$tag->color}};background-color:{{$tag->background_color}};">
                {{$tag->name}}
            </span>
            @endforeach
        </div>
    </div>
    @if (session('pool-forum-status'))
        <div class="bg-green-500 text-white px-6 py-2 rounded">
            {{ session('pool-forum-status') }}
        </div>
    @endif
    @livewire('forum.comments', ['discussion'=>$discussion,'posts' => $posts])

    @if(Auth::check())
        <div class="">
            @if(!$discussion->is_locked)
                @livewire('forum.comment', ['discussion' => $discussion,'user'=>Auth::user()->name])
            @else
                <div class="col text-center text-gray-500">
                    Discussion locked by owner
                </div>
            @endif
        </div>
    @endif
</div>

@include('pool-forum::'.config('pool-forum.views.folder').'shared.scripts.avatar')
@include('pool-forum::'.config('pool-forum.views.folder').'shared.scripts.input-boolean')
<script type="text/javascript">
    function toggleEdit(postId) {
        const content = document.getElementById('post-content-' + postId);
        const form = document.getElementById('post-form-' + postId);

        const addContent = document.getElementById('post-content');
        const addSubmit = document.querySelectorAll('#post-form [type=submit]')[0];

        if (form.classList.contains('hidden')) {
            content.classList.remove('block');
            content.classList.add('hidden');
            form.classList.remove('hidden');
            form.classList.add('block');
            addContent.disabled = true;
            addSubmit.disabled = true;
        } else {
            content.classList.remove('hidden');
            content.classList.add('block');
            form.classList.remove('block');
            form.classList.add('hidden');
            addContent.disabled = false;
            addSubmit.disabled = false;
        }
    }

    function canEdit(postId) {
        const submit = document.querySelectorAll('#post-form-' + postId + ' [type=submit]')[0];
        const textarea = document.querySelectorAll('#post-form-' + postId + ' [name=content]')[0];
        const data = textarea.value.trim();
        const old = textarea.getAttribute('old').trim();

        submit.disabled = !(data.length > 0 && data !== old);
    }
</script>
