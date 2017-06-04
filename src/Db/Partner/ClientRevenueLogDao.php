<?php

namespace Bike\Partner\Db\Partner;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

use Bike\Partner\Db\AbstractDao;
use Bike\Partner\Util\ArgUtil;

class ClientRevenueLogDao extends AbstractDao
{
    protected function parseTable($cond, $dbOp)
    {
        return "`{$this->db}`.`{$this->prefix}client_revenue_log`";
    }

    protected function applyWhere(QueryBuilder $qb, array $where, $dbOp)
    {
        $where = ArgUtil::getArgs($where, array(
            'client_id',
            'log_day',
            'start_time',
            'end_time',
        )); 

        if ($where['client_id']) {
            $qb->andWhere('client_id = ' . $qb->createNamedParameter($where['client_id']));
        }
        if ($where['log_day']) {
            $qb->andWhere('log_day = ' . $qb->createNamedParameter($where['log_day']));
        }
        if ($where['start_time']) {
            $qb->andWhere('log_day >= ' . $qb->createNamedParameter($where['start_time']));
        }
        if ($where['end_time']) {
            $qb->andWhere('log_day <= ' . $qb->createNamedParameter($where['end_time']));
        }
    }

    protected function applyOrder(QueryBuilder $qb, array $order)
    {

    }

    protected function applyGroup(QueryBuilder $qb, array $group)
    {

    }
}
