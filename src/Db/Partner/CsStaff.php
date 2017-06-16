<?php

namespace Bike\Partner\Db\Partner;

use Bike\Partner\Db\AbstractEntity;

class CsStaff extends AbstractEntity
{
    protected static $pk = 'id';

    const LEVEL_ONE = 1;//一级
    const LEVEL_TWO = 2;//二级
    const LEVEL_THREE = 3;//三级

    protected static $cols = array(
        'id' => null,
        'name' => null,
        'parent_id' => null,
        'parent_ids' => '',
        'level' => null,
    );
}
