<?php

namespace Bike\Partner\Db\Primary;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

use Bike\Partner\Db\AbstractDao;
use Bike\Partner\Util\ArgUtil;

class BikeDao extends AbstractDao
{
    protected function parseTable($cond, $dbOp)
    {
        return "`{$this->db}`.`{$this->prefix}bike`";
    }

    protected function applyWhere(QueryBuilder $qb, array $where, $dbOp)
    {
        $where = ArgUtil::getArgs($where, array(
            'client_id',
            'agent_id',
            'sn',
        )); 

        if ($where['client_id']) {
            $qb->andWhere('client_id = ' . $qb->createNamedParameter($where['client_id']));
        }
        if ($where['agent_id']) {
            $qb->andWhere('agent_id = ' . $qb->createNamedParameter($where['agent_id']));
        }
        if ($where['sn']) {
            $qb->andWhere('sn = ' . $qb->createNamedParameter($where['sn']));
        }
    }

    protected function applyOrder(QueryBuilder $qb, array $order)
    {

    }

    protected function applyGroup(QueryBuilder $qb, array $group)
    {

    }
}
