<?php

namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;
use Bike\Partner\Db\Primary\Bike as PrimaryBike;
use Bike\Partner\Db\Partner\Bike as PartnerBike;
use Bike\Partner\Db\Primary\BikeIdGenrator;
use Bike\Partner\Db\Partner\Passport;

class BikeService extends AbstractService
{
    public function createBike(array $data)
    {
        $primaryBikeDao = $this->getPrimaryBikeDao();
        $primaryBikeConn = $primaryBikeDao->getConn();
        $partnerBikeDao = $this->getPartnerBikeDao();
        $partnerBikeConn = $partnerBikeDao->getConn();
        $bikeIdGeneratorDao = $this->getBikeIdGeneratorDao();
        $bikeIdGeneratorConn = $bikeIdGeneratorDao->getConn();

        $primaryBikeConn->beginTransaction();
        $partnerBikeConn->beginTransaction();
        $bikeIdGeneratorConn->beginTransaction();
        try {
            $id = $this->generateBikeId();
            $primaryBike = new PrimaryBike();
            $time = time();
            $primaryBike
                ->setId($id)
                ->setCreateTime($time);
            $primaryBikeDao->create($primaryBike);

            $partnerBike = new PartnerBike();
            $partnerBike
                ->setId($id)
                ->setCreateTime($time);
            $partnerBikeDao->create($partnerBike);

            $primaryBikeConn->commit();
            $partnerBikeConn->commit();
            $bikeIdGeneratorConn->commit();
        } catch (\Exception $e) {
            $primaryBikeConn->rollBack();
            $partnerBikeConn->rollBack();
            $bikeIdGeneratorConn->rollBack();
            throw $e;
        }
    }


    public function bindBike($id, $clientId, $username = '')
    {
        try {
            $bikeDao = $this->getPartnerBikeDao();
            
            $bike = $bikeDao->find($id);
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

    public function unbindBike($id)
    {
        try {
            $bikeDao = $this->getPartnerBikeDao();
            $bike = $bikeDao->find($id);
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
        $bikeDao = $this->getPartnerBikeDao();
        $bikeList = $bikeDao->findList('*', $args, $offset, $pageNum, array(
            'id' => 'desc',
        ));
        if ($bikeList) {
            $agentIds = array();
            $clientIds = array();
            foreach ($bikeList as $v) {
                $agentIds[] = $v->getAgentId();
                $clientIds[] = $v->getClientId();
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

    public function getBikeById($id)
    {
        $key = 'bike.id.' . $id;
        $bike = $this->getRequestCache($key);
        if (!$bike) {
            $bikeDao = $this->getPartnerBikeDao();
            $bike = $bikeDao->find($id);
            if ($bike) {
                $this->setRequestCache($key, $bike);
            }
        }
        return $bike;
    }

    protected function generateBikeId()
    {
        $bikeIdGeneratorDao = $this->getBikeIdGeneratorDao();
        return $bikeIdGeneratorDao->save(array(), true);
    }

    protected function getPartnerBikeDao()
    {
        return $this->container->get('bike.partner.dao.partner.bike');
    }

    protected function getPrimaryBikeDao()
    {
        return $this->container->get('bike.partner.dao.primary.bike');
    }

    protected function getBikeIdGeneratorDao()
    {
        return $this->container->get('bike.partner.dao.primary.bike_id_generator');
    }
}
 
