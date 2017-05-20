<?php

namespace Bike\Partner\Db\Partner;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

use Bike\Partner\Db\AbstractDao;

use Bike\Partner\Util\ArgUtil;

class CsStaffDao extends AbstractDao
{
    protected function parseTable($cond, $dbOp)
    {
        return "`{$this->db}`.`{$this->prefix}cs_staff`";
    }

    protected function applyWhere(QueryBuilder $qb, array $where, $dbOp)
    {
        $where = ArgUtil::getArgs($where, array(
            'level', 
            'id_not',
        ));
        if ($where['level']) {
            $qb->andWhere('level = ' . $qb->createNamedParameter($where['level']));
        }
        if ($where['id_not']) {
            $qb->andWhere('id <> ' . $qb->createNamedParameter($where['id_not']));
        }
    }

    protected function applyOrder(QueryBuilder $qb, array $order)
    {

    }

    protected function applyGroup(QueryBuilder $qb, array $group)
    {

    }
}
