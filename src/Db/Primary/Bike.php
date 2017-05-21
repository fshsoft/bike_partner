<?php

namespace Bike\Partner\Db\Primary;

use Bike\Partner\Db\AbstractEntity;

class Bike extends AbstractEntity
{
    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'sn' => null,
        'elock_sn' => 0,
        'client_id' => 0,
        'agent_id' => 0,
        'create_time' => null,
    );
}
