<?php

namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;
use Bike\Partner\Db\Partner\Client;
use Bike\Partner\Db\Partner\Passport;

class ClientService extends AbstractService
{
    public function createClient(array $data)
    {
        $data = ArgUtil::getArgs($data, array(
            'name',
            'username',
            'pwd',
            'repwd',
        ));
        $data['type'] = Passport::TYPE_CLIENT;

        $this->validateName($data['name']);
        $clientDao = $this->getClientDao();
        $clientConn = $clientDao->getConn();
        $passportService = $this->container->get('bike.partner.service.passport');
        $passportDao = $this->container->get('bike.partner.dao.partner.passport');
        $passportConn = $passportDao->getConn();
        $clientConn->beginTransaction();
        $passportConn->beginTransaction();
        try {
            $passportId = $passportService->createPassport($data);
            $client = new Client($data);
            $client->setId($passportId);
            $clientDao->create($client);

            $passportConn->commit();
            $clientConn->commit();
        } catch (\Exception $e) {
            $passportConn->rollBack();
            $clientConn->rollBack();
            throw $e;
        }
    }

    public function editClient($id,array $data)
    {
        $data = ArgUtil::getArgs($data, array(
            'name',
            'username',
            'pwd',
            'repwd',
        ));
        $data['type'] = Passport::TYPE_CLIENT;
        $data['id'] = $id;

        $this->validateName($data['name']);
        $clientDao = $this->getClientDao();
        $clientConn = $clientDao->getConn();
        $passportService = $this->container->get('bike.partner.service.passport');
        $passportDao = $this->container->get('bike.partner.dao.partner.passport');
        $passportConn = $passportDao->getConn();
        $clientConn->beginTransaction();
        $passportConn->beginTransaction();
        try {
            $passportService->updatePassport($id,$data);
            $client = new Client($data);
            $clientDao->save($client);

            $passportConn->commit();
            $clientConn->commit();
        } catch (\Exception $e) {
            $passportConn->rollBack();
            $clientConn->rollBack();
            throw $e;
        }
    }


    public function getClient($id)
    {
        $key = 'client.' . $id;
        $client = $this->getRequestCache($key);
        if (!$client) {
            $clientDao = $this->getClientDao();
            $client = $clientDao->find($id);
            if ($client) {
                $this->setRequestCache($key, $client);
            }
        }
        return $client;
    }

    public function searchClient(array $args, $page, $pageNum)
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
        $clientDao = $this->getClientDao();
        $clientList = $clientDao->findList('*', array(), $offset, $pageNum);
        if ($clientList) {
            $passportIds = array();
            foreach ($clientList as $v) {
                $passportIds[] = $v->getId();
            }
            $passportDao = $this->container->get('bike.partner.dao.partner.passport');
            $passportMap = $passportDao->findMap('', array(
                'id.in' => $passportIds,
            ), 0, 0);
        } else {
            $passportMap = array();
            $clientList = array();
        }
        $total = $clientDao->findNum(array());
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
                'client' => $clientList,
            ),
            'map' => array(
                'passport' => $passportMap,
            ),
        );
    }

    protected function validateName($name)
    {
        if (!$name) {
            throw new LogicException('委托人名称不能为空');
        }
        $len = mb_strlen($name);
        if ($len > 20) {
            throw new LogicException('委托人名称不能多于20个字符');
        }
    }

    protected function getClientDao()
    {
        return $this->container->get('bike.partner.dao.partner.client');
    }
}
 
