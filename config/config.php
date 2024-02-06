<?php

return [
    'name_prefix' => 'forum.',
    'table_names' => [
        'settings' => 'forum_settings',
        'discussions' => 'forum_discussions',
        'discussion_users' => 'forum_discussion_user',
        'posts' => 'forum_posts',
        'tags' => 'forum_tags',
        'discussion_tags' => 'forum_discussion_tag',
    ],
    'models' => [
        'user' => 'App\Models\User',
    ],
    'views' => [
        'folder' => 'tw.',
    ],
    'roles' => [
        'admin' => 'admin', //laravel-permissions Admin Role
    ],
];
