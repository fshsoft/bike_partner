<?php

namespace Bike\Partner\Db\Partner;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

use Bike\Partner\Db\AbstractDao;
use Bike\Partner\Util\ArgUtil;

class PassportDao extends AbstractDao
{
    protected function parseTable($cond, $dbOp)
    {
        return "`{$this->db}`.`{$this->prefix}passport`";
    }

    protected function applyWhere(QueryBuilder $qb, array $where, $dbOp)
    {
        $where = ArgUtil::getArgs($where, array(
            'username',
            'id.in',
        ));
        if ($where['username']) {
            $qb->andWhere('username = ' . $qb->createNamedParameter($where['username']));
        }
        if ($where['id.in']) {
            $qb->andWhere('id IN (' . $qb->createNamedParameter($where['id.in'], Connection::PARAM_INT_ARRAY) . ')');
        }
    }

    protected function applyOrder(QueryBuilder $qb, array $order)
    {
        if (!$order) { // 默认id倒序 
            $qb->orderBy('id', 'DESC');
        }
    }

    protected function applyGroup(QueryBuilder $qb, array $group)
    {

    }
}
