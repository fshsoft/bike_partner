<?php

namespace Bike\Partner\Db\Primary;

use Bike\Partner\Db\AbstractEntity;

class Bike extends AbstractEntity
{
    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'sn' => null,
        'elock_sn' => null,
        'client_id' => null,
        'agent_id' => null,
        'create_time' => null,
    );
}
