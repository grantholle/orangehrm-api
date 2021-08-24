<?php

namespace GrantHolle\OrangeHrm;

use Illuminate\Support\Facades\Facade;

/**
 * @see \GrantHolle\OrangeHrm\OrangeHrm
 */
class OrangeHrmFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'orangehrm-api';
    }
}
