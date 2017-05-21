<?php

namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;
use Bike\Partner\Db\Primary\Bike;
use Bike\Partner\Db\Primary\BikeSnGenrator;

class BikeService extends AbstractService
{
    public function createBike(array $data)
    {
        $bikeDao = $this->getBikeDao();
        $bikeConn = $bikeDao->getConn();
        $bikeSnGeneratorDao = $this->getBikeSnGeneratorDao(); 
        $bikeSnGeneratorConn = $bikeSnGeneratorDao->getConn();

        $bikeConn->beginTransaction();
        $bikeSnGeneratorConn->beginTransaction();
        try {
            $sn = $this->generateBikeSn();
            $bike = new Bike();
            $bike
                ->setSn($sn)
                ->setCreateTime(time());
            $bikeDao->create($bike);
            $bikeConn->commit();
            $bikeSnGeneratorConn->commit();
        } catch (\Exception $e) {
            $bikeConn->rollBack();
            $bikeSnGeneratorConn->rollBack();
            throw $e;
        }
    }

    public function searchBike(array $args, $page, $pageNum)
    {
        $page = intval($page);
        $pageNum = intval($pageNum);
        if ($page < 1) {
            $page = 1;
        }
        if ($pageNum < 1) {
            $pageNum = 1;
        }
        $offset = ($page - 1) * $pageNum;
        $bikeDao = $this->getBikeDao();
        $bikeList = $bikeDao->findList('*', $args, $offset, $pageNum);
        if ($bikeList) {
            $agentIds = array();
            $clientIds = array();
            foreach ($bikeList as $v) {
                $agentIds[] = $v->getId();
                $clientIds[] = $v->getId();
            }
            $agentDao = $this->container->get('bike.partner.dao.partner.agent');
            $agentMap = $agentDao->findMap('', array(
                'id.in' => $agentIds,
            ), 0, 0);
            $clientDao = $this->container->get('bike.partner.dao.partner.client');
            $clientMap = $clientDao->findMap('', array(
                'id.in' => $clientIds,
            ), 0, 0);
        } else {
            $bikeList = $agentMap = $clientMap = array();
        }
        $total = $bikeDao->findNum($args);
        if ($total) {
            $totalPage = ceil($total / $pageNum);
            if ($page > $totalPage) {
                $page = $totalPage;
            }
        } else {
            $totalPage = 1;
            $page = 1;
        }

        return array(
            'page' => $page,
            'totalPage' => $totalPage,
            'pageNum' => $pageNum,
            'total' => $total,
            'list' => array(
                'bike' => $bikeList,
            ),
            'map' => array(
                'agent' => $agentMap,
                'client' => $clientMap,
            ),
        );
    }

    protected function generateBikeSn()
    {
        $bikeSnGeneratorDao = $this->getBikeSnGeneratorDao();
        return $bikeSnGeneratorDao->create(array(), true);
    }

    protected function getBikeDao()
    {
        return $this->container->get('bike.partner.dao.primary.bike');
    }

    protected function getBikeSnGeneratorDao()
    {
        return $this->container->get('bike.partner.dao.primary.bike_sn_generator');
    }
}
 
