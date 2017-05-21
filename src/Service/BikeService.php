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
            foreach ($bikeList as $v) {
                $agentIds[] = $v->getId();
            }
            $passportDao = $this->container->get('bike.partner.dao.partner.passport');
            $passportMap = $passportDao->findMap('', array(
                'id.in' => $passportIds,
            ), 0, 0);
        } else {
            $passportMap = array();
            $adminList = array();
        }
        $total = $adminDao->findNum(array());
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
                'admin' => $adminList,
            ),
            'map' => array(
                'passport' => $passportMap,
            ),
        );
    }

    protected function generateBikeSn()
    {
        $bikeSnGeneratorDao = $this->container->get('bike.partner.dao.primary');
        $bikeSnGenerator = new BikeSnGenerator();
        return $bikeSnGeneratorDao->create($bikeSnGenerator, true);
    }

    protected function getBikeDao()
    {
        return $this->container->get('bike.partner.dao.primary.bike');
    }
}
 
