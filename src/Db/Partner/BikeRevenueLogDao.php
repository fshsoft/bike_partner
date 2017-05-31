<?php

namespace Bike\Partner\Db\Partner;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

use Bike\Partner\Db\AbstractDao;
use Bike\Partner\Util\ArgUtil;

class BikeRevenueLogDao extends AbstractDao
{
    protected function parseTable($cond, $dbOp)
    {
        return "`{$this->db}`.`{$this->prefix}bike_revenue_log`";
    }

    protected function applyWhere(QueryBuilder $qb, array $where, $dbOp)
    {
        $where = ArgUtil::getArgs($where, array(
            'client_id',
            'agent_id',
            'log_date',
        )); 

        if ($where['client_id']) {
            $qb->andWhere('client_id = ' . $qb->createNamedParameter($where['client_id']));
        }
        if ($where['agent_id']) {
            $qb->andWhere('agent_id = ' . $qb->createNamedParameter($where['agent_id']));
        }
        if ($where['log_date']) {
            $qb->andWhere('log_date = ' . $qb->createNamedParameter($where['log_date']));
        }
    }

    protected function applyOrder(QueryBuilder $qb, array $order)
    {
        if (!empty($order)) {
            foreach ($order as $key => $value) {
                $qb->addOrderBy($key,$value);
            }
        }
    }

    protected function applyGroup(QueryBuilder $qb, array $group)
    {
        if (!empty($group)) {
            foreach ($group as $each) {
                $qb->addGroupBy($each);
            }
        }
    }
}
