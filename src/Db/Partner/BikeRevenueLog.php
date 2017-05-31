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
        'bike_sn' => null,
        'agent_id' => null,
        'client_id' => null,
        'type' => null,
        'amount' => null,
        'date' => null,
    );
}
