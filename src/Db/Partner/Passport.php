<?php

namespace Bike\Partner\Db\Partner;

use Bike\Common\Db\AbstractEntity;

class Passport extends AbstractEntity
{
    const ROLE_ADMIN = 1;
    const ROLE_YUNWEI = 2;
    const ROLE_YIYUAN = 3;

    protected static $pk = 'id';

    protected static $cols = array(
        'id' => null,
        'username' => null,
        'pwd' => null,
        'role' => null,
        'last_login_ip' => null,
        'last_login_time' => null,
        'create_time' => null,
    );
}