<?php

namespace Dimimo\PoolForum\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Dimimo\PoolForum\Models\Discussion;
use Dimimo\PoolForum\Models\Post;

class CommentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Authenticatable $user;

    public Discussion $discussion;

    public Post $post;

    public string $action = 'created';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Authenticatable $user, Discussion $discussion, Post $post, $action = 'created')
    {
        $this->user = $user;
        $this->discussion = $discussion;
        $this->post = $post;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
