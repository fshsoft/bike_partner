<?php

namespace Bike\Partner\Db\Partner;

use Bike\Partner\Db\AbstractEntity;

class Bike extends AbstractEntity
{
    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'elock_id' => 0,
        'client_id' => 0,
        'user_id' => 0,
        'agent_id' => 0,
        'static_revenue' => 0,
        'dynamic_revenue' => 0,
        'revenue' => 0,
        'status' => 1,
        'lat' => '',
        'lng' => '',
        'create_time' => null,
    );
}
