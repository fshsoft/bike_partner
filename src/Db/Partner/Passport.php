<?php

namespace Bike\Partner\Db\Partner;

use Bike\Partner\Db\AbstractEntity;

class Passport extends AbstractEntity
{
    const TYPE_ADMIN = 1;
    const TYPE_CS_STAFF = 2;
    const TYPE_AGENT = 3;
    const TYPE_CLIENT = 4;

    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'username' => null,
        'pwd' => null,
        'type' => null,
        'last_login_ip' => '',
        'last_login_time' => 0,
        'create_time' => null,
    );
}
