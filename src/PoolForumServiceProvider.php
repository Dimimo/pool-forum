<?php

namespace Dimimo\PoolForum;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Livewire\Livewire;
use Dimimo\PoolForum\Http\Livewire\Forum\Comment;
use Dimimo\PoolForum\Http\Livewire\Forum\CommentEdit;
use Dimimo\PoolForum\Http\Livewire\Forum\Comments;

class PoolForumServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pool-forum');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'pool-forum');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('pool-forum.php'),
            ], ['config', 'pool-forum']);

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/pool-forum'),
            ], ['views', 'pool-forum']);

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], ['migrations', 'pool-forum']);

            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/pool-forum'),
            ], ['lang', 'pool-forum']);

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/pool-forum'),
            ], 'assets');*/
        }
        Livewire::component('forum.comment', Comment::class);
        Livewire::component('forum.comment-edit', CommentEdit::class);
        Livewire::component('forum.comments', Comments::class);

        Str::macro('initials', function ($string, $number = 2) {
            $words = preg_split("/[\s,_-]+/", $string);
            $number = (count($words) > $number) ? $number : count($words);
            $acronym = '';
            for ($i = 0; $i < $number; $i++) {
                $acronym .= $words[$i][0];
            }

            return $acronym;
        });
        Stringable::macro('initials', function ($number = 2) {
            return new static($number);
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'pool-forum');

        $this->app->singleton('pool-forum', function () {
            return new PoolForum();
        });
    }
}
