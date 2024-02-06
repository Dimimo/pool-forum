<?php

namespace Dimimo\PoolForum;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class PoolForum
{
    public static function routes(): void
    {
        Route::name(config('pool-forum.name_prefix'))->group(function () {
            $controller = '\Dimimo\PoolForum\Http\Controllers\\';

            App::make('router')
                ->get('discussions/status', $controller.'DiscussionController@statusAll')
                ->name('discussions.status.all');
            App::make('router')
                ->get('discussions/{discussion}/status', $controller.'DiscussionController@status')
                ->name('discussions.status');
            App::make('router')
                ->get('discussions/create', $controller.'DiscussionController@create')
                ->name('discussions.create');
            App::make('router')
                ->get('posts/{post}/status', $controller.'PostController@status')
                ->name('posts.status');
            App::make('router')
                ->get('/', $controller.'DiscussionController@index')
                ->name('forum.index');

            App::make('router')->resource('discussions', $controller.'DiscussionController');
            App::make('router')->resource('settings', $controller.'SettingController');
            App::make('router')->resource('tags', $controller.'TagController');
            App::make('router')->resource('posts', $controller.'PostController');

        });
    }

    public static function apiRoutes(): void
    {
        App::make('router')->apiResource('settings', '\Dimimo\PoolForum\Http\Controllers\API\SettingController');
    }
}
