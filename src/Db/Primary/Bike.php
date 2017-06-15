<?php

namespace Bike\Partner\Db\Primary;

use Bike\Partner\Db\AbstractEntity;

class Bike extends AbstractEntity
{
    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'elock_id' => 0,
        'user_id' => 0,
        'status' => 1,
        'lat' => '',
        'lng' => '',
        'create_time' => null,
    );
}
