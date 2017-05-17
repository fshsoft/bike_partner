<?php

namespace Bike\Partner\Db\Partner;

use Bike\Partner\Db\AbstractEntity;

class Agent extends AbstractEntity
{
    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'name' => null,
        'parent_id' => null,
        'level' => null,
    );
}