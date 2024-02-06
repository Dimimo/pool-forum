<?php

namespace Dimimo\PoolForum\Http\Livewire\Forum;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Dimimo\PoolForum\Events\CommentEvent;
use Dimimo\PoolForum\Models\Post;

class Comment extends Component
{
    public $discussion;

    public $avatar;

    public $content;

    const COMMENT_UPLOADED = 'commentUploaded';

    public function mount($discussion, $user)
    {
        $this->discussion = $discussion;
        $this->avatar = $user;
    }

    public function render(): View
    {
        return view('pool-forum::tw.livewire.forum.comment');
    }

    public function save(Request $request)
    {
        $this->validate([
            'content' => 'required|min:2',
        ]);
        $discussion = $this->discussion;
        $data = [];
        $data['discussion_id'] = $discussion->id;
        $data['content'] = $this->content;
        $data['user_id'] = Auth::user()->id;
        $data['is_private'] = 0;
        $data['is_approved'] = 1;
        $data['number'] = $discussion->post_number_index + 1;
        $data['ip_address'] = $request->ip();
        $post = Post::create($data);

        $discussion->comment_count = ($discussion->comment_count + 1);
        $discussion->participant_count = count($discussion->posts()->get()->unique('user_id'));
        $discussion->post_number_index = $post->number;
        $discussion->last_posted_at = $post->created_at;
        $discussion->last_posted_user_id = $post->user_id;
        $discussion->last_post_id = $post->id;

        if ($post->number === 1) {
            $discussion->first_post_id = $post->id;
        }
        $discussion->save();
        $this->discussion = $discussion;
        $this->content = '';

        $this->dispatch(self::COMMENT_UPLOADED);
        CommentEvent::dispatch(Auth::user(), $discussion, $post);
    }
}
