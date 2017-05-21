<?php

namespace Bike\Partner\Db\Primary;

use Bike\Partner\Db\AbstractEntity;

class Elock extends AbstractEntity
{
    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'sn' => null,
        'bike_sn' => 0,
        'create_time' => null,
    );
}
