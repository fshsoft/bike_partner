<?php

namespace Bike\Partner\Db\Partner;

use Bike\Partner\Db\AbstractEntity;

class AgentRevenueLog extends AbstractEntity
{
    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'agent_id' => null,
        'type' => null,
        'revenue' => 0,
        'log_day' => '',
        'log_month' => null,
        'log_time' => null,
    );
}
