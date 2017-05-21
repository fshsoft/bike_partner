<?php

namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;
use Bike\Partner\Db\Primary\Bike;
use Bike\Partner\Db\Primary\BikeSnGenrator;
use Bike\Partner\Db\Partner\Passport;

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


    public function bindBike($sn, $clientId, $username = '')
    {
        try {
            $bikeDao = $this->getBikeDao();
            $where = ['sn'=>$sn];
            $bike = $bikeDao->find($where);
            if (!$bike) {
                throw new LogicException("未找到车辆");
            }
            if ($bike->getClientId() > 0) {
                throw new LogicException("车辆已被分配");
            }

            if ($clientId) {
                $clientDao = $this->container->get('bike.partner.dao.partner.client');
                $client = $clientDao->find($clientId);    
            } elseif ($username) {
                $passportDao = $this->container->get('bike.partner.dao.partner.passport');
                $wherePass = ['username'=>$username,'type'=>Passport::TYPE_CLIENT];
                $client = $passportDao->find($wherePass);
            } else {
                throw new LogicException("参数错误");
            }
            
            if (!$client) {
                throw new LogicException("没有找到委托人");
            }

            $data = ['client_id'=>$client->getId()];
            $bikeDao->update($bike->getId(),$data);

        } catch (\Exception $e) {
            throw $e;
        }
       
    }

    public function unbindBike($sn)
    {
        try {
            $bikeDao = $this->getBikeDao();
            $where = ['sn'=>$sn];
            $bike = $bikeDao->find($where);
            if (!$bike) {
                throw new LogicException("未找到车辆");
            }
            if ($bike->getClientId() <= 0) {
                throw new LogicException("车辆未被分配");
            }

            $data = ['client_id'=>0];
            $bikeDao->update($bike->getId(),$data);

        } catch (\Exception $e) {
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
        $bikeList = $bikeDao->findList('*', $args, $offset, $pageNum, array(
            'sn' => 'desc',
        ));
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

    public function getBikeBySn($sn)
    {
        $key = 'bike.sn.' . $sn;
        $bike = $this->getRequestCache($key);
        if (!$bike) {
            $bikeDao = $this->getBikeDao();
            $bike = $bikeDao->find(array(
                'sn' => $sn,
            ));
            if ($bike) {
                $this->setRequestCache($key, $bike);
            }
        }
        return $bike;
    }

    protected function generateBikeSn()
    {
        $bikeSnGeneratorDao = $this->getBikeSnGeneratorDao();
        return $bikeSnGeneratorDao->save(array(), true);
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
 
