<?php

namespace Bike\Partner\Db\Partner;

use Bike\Partner\Db\AbstractEntity;

class Passport extends AbstractEntity
{
    const ROLE_ADMIN = 1;
    const ROLE_CS_STAFF = 2;
    const ROLE_AGENT = 3;
    const ROLE_CLIENT = 4;

    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'username' => null,
        'pwd' => null,
        'role' => null,
        'last_login_ip' => '',
        'last_login_time' => 0,
        'create_time' => null,
    );
}
