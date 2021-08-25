<?php

namespace GrantHolle\OrangeHrm;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin OrangeHrm
 */
class OrangeHrmFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return OrangeHrm::class;
    }
}
