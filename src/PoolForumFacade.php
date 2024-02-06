<?php

namespace Dimimo\PoolForum;

use Illuminate\Support\Facades\Facade;

class PoolForumFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'pool-forum';
    }
}
