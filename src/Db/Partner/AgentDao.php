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
            'parent_id',
            'level',
            'name',
            'id.in',
            'id.not',
            'parent_id.in',
        ));
        if ($where['parent_id']) {
            $qb->andWhere('parent_id = ' . $qb->createNamedParameter($where['parent_id']));
        }
        if ($where['level']) {
            $qb->andWhere('level = ' . $qb->createNamedParameter($where['level']));
        }
        if ($where['name']) {
            $qb->andWhere('name like :likename')->setParameter(':likename','%'.$where['name'].'%');
        }
        if ($where['id.not']) {
            $qb->andWhere('id <> ' . $qb->createNamedParameter($where['id.not']));
        }
        if ($where['parent_id.in']) {
            $qb->andWhere('parent_id IN (' . $qb->createNamedParameter($where['parent_id.in'], Connection::PARAM_INT_ARRAY) . ')');
        }
    }

    protected function applyOrder(QueryBuilder $qb, array $order)
    {

    }

    protected function applyGroup(QueryBuilder $qb, array $group)
    {

    }
}
