<?php

namespace Bike\Partner\Db\Primary;

use Bike\Partner\Db\AbstractEntity;

class Bike extends AbstractEntity
{
    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'sn' => null,
        'elock_sn' => '',
        'client_id' => 0,
        'agent_id' => 0,
        'static_revenue' => '0.00',
        'dynamic_revenue' => '0.00',
        'revenue' => '0.00',
        'create_time' => null,
    );
}
