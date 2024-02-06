<?php

namespace Dimimo\PoolForum\Http\Livewire\Forum;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Dimimo\PoolForum\Events\CommentEvent;

class CommentEdit extends Component
{
    public $post;

    public $comment;

    const COMMENT_UPDATED = 'commentUpdated';

    public function mount($post)
    {
        $this->post = $post;
        $this->comment = $post->content;
    }

    public function render(): View
    {
        return view('pool-forum::tw.livewire.forum.comment-edit', ['post' => $this->post]);
    }

    public function update()
    {
        $this->post->content = $this->comment;
        $this->post->save();
        $this->dispatch(self::COMMENT_UPDATED);
        CommentEvent::dispatch(Auth::user(), $this->post->discussion, $this->post, 'updated');
    }
}
