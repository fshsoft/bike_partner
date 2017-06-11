<?php

namespace Bike\Partner\Db\Partner;

use Bike\Partner\Db\AbstractEntity;

class Agent extends AbstractEntity
{
    protected static $pk = 'id';

	const LEVEL_ONE = 1;//一级
    const LEVEL_TWO = 2;//二级
    const LEVEL_THREE = 3;//三级

    protected static $cols = array(
        'id' => null,
        'name' => null,
        'parent_id' => null,
        'parent_ids' => null,
        'level' => null,
        'static_revenue' => 0,
        'dynamic_revenue' => 0,
        'revenue' => 0,
    );
}
