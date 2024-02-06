<?php

namespace Dimimo\PoolForum\Http\Livewire\Forum;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Dimimo\PoolForum\Models\Post;

class Comments extends Component
{
    public $listeners = [Comment::COMMENT_UPLOADED => 'reload', CommentEdit::COMMENT_UPDATED => 'reload'];

    public $discussion;

    public $comment = [];

    public $posts = [];

    public function mount($discussion)
    {
        $this->discussion = $discussion;
        $this->comment = [];
    }

    public function render(): View
    {
        $data = ['posts' => $this->getPosts()];

        return view('pool-forum::tw.livewire.forum.comments', $data);
    }

    public function reload()
    {
        $this->posts = $this->getPosts();
    }

    protected function getPosts()
    {
        return ($this->discussion->canEdit())
         ? $this->discussion->posts()->orderBy('created_at', 'ASC')->get()
         : $this->discussion->posts()->where('is_approved', 1)
             ->orderBy('created_at', 'ASC')
             ->get();
    }

    public function delete($id)
    {
        $post = Post::find($id);
        $post->delete();
        $this->posts = $this->getPosts();
    }

    public function status($id, $key, $value)
    {
        $post = Post::find($id);
        if (! $post->canEdit(Auth::user()->id)) {
            abort(403);

        }
        switch ($key) {
            case 'approve':
                $post->is_approved = $value ? 1 : 0;
                break;
            case 'private':
                $post->is_private = $value === true ? 1 : 0;
                break;
            case 'hide':
                if ($value) {
                    $post->hidden_at = null;
                    $post->hidden_user_id = null;
                } else {
                    $post->hidden_at = Carbon::now()->toDateTimeString();
                    $post->hidden_user_id = Auth::user()->id;
                }
                break;
        }
        $post->save();
        $this->posts = $this->getPosts();
    }
}
