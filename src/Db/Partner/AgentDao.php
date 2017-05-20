<?php

namespace Bike\Partner\Db\Partner;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

use Bike\Partner\Db\AbstractDao;
use Bike\Partner\Util\ArgUtil;

class AgentDao extends AbstractDao
{
    protected function parseTable($cond, $dbOp)
    {
        return "`{$this->db}`.`{$this->prefix}agent`";
    }

    protected function applyWhere(QueryBuilder $qb, array $where, $dbOp)
    {
        $where = ArgUtil::getArgs($where, array(
            'id',
            'id.in',
            'level',
            'id.not',
        ));
        if ($where['level']) {
            $qb->andWhere('level = ' . $qb->createNamedParameter($where['level']));
        }
        if ($where['id.not']) {
            $qb->andWhere('id <> ' . $qb->createNamedParameter($where['id.not']));
        }
    }

    protected function applyOrder(QueryBuilder $qb, array $order)
    {

    }

    protected function applyGroup(QueryBuilder $qb, array $group)
    {

    }
}
