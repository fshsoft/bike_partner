<?php

namespace Bike\Partner\Db\Partner;

use Bike\Partner\Db\AbstractEntity;

class Client extends AbstractEntity
{
    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'name' => null,
        'static_revenue' => 0,
        'dynamic_revenue' => 0,
        'revenue' => 0,
    );
}
