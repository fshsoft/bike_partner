<?php

namespace Bike\Partner\Db\Partner;

use Bike\Partner\Db\AbstractEntity;

class BikeRevenueLog extends AbstractEntity
{
    const TYPE_DYNAMIC = 1;
    const TYPE_STATIC = 2;

    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'bike_id' => null,
        'agent_id' => null,
        'client_id' => null,
        'agent_percent' => 0,
        'client_percent' => 0,
        'type' => null,
        'revenue' => 0,
        'log_day' => '',
        'log_month' => null,
        'log_time' => null,
    );
}
