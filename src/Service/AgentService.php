<?php

namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;
use Bike\Partner\Db\Partner\Agent;
use Bike\Partner\Db\Partner\Passport;

class AgentService extends AbstractService
{
    public function createAgent(array $data)
    {
        $data = ArgUtil::getArgs($data, array(
            'name',
            'username',
            'pwd',
            'repwd',
            'parent_id',
        ));
        $data['type'] = Passport::TYPE_AGENT;

        $this->validateName($data['name']);
        $this->validateParentId($data['parent_id']);
        if ($data['parent_id'] == 0) {
            $data['level'] = 1;
        } else {
            $agent = $this->getAgent($data['parent_id']);
            $data['level'] = $agent->getLevel() + 1;
        }

        $agentDao = $this->getAgentDao();
        $agentConn = $agentDao->getConn();
        $passportService = $this->container->get('bike.partner.service.passport');
        $passportDao = $this->container->get('bike.partner.dao.partner.passport');
        $passportConn = $passportDao->getConn();
        $agentConn->beginTransaction();
        $passportConn->beginTransaction();
        try {
            $passportId = $passportService->createPassport($data);
            $agent = new Agent($data);
            $agent->setId($passportId);
            $agentDao->create($agent);

            $passportConn->commit();
            $agentConn->commit();
        } catch (\Exception $e) {
            $passportConn->rollBack();
            $agentConn->rollBack();
            throw $e;
        }
    }

    public function searchAgent(array $args, $page, $pageNum)
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
        $agentDao = $this->getAgentDao();
        $agentList = $agentDao->findList('*', array(), $offset, $pageNum);
        if ($agentList) {
            $passportIds = array();
            foreach ($agentList as $v) {
                $passportIds[] = $v->getId();
            }
            $passportService = $this->container->get('bike.partner.service.passport');
            $passportMap = $passportService->getPassportMapByIds('', $passportIds);
        } else {
            $passportMap = array();
            $agentList = array();
        }
        $total = $agentDao->findNum(array());
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
                'agent' => $agentList,
            ),
            'map' => array(
                'passport' => $passportMap,
            ),
        );
    }

    public function getAgent($id)
    {
        $key = 'agent.' . $id;
        $agent = $this->getRequestCache($key);
        if (!$agent) {
            $agentDao = $this->getAgentDao();
            $agent = $agentDao->find($id);
            if ($agent) {
                $this->setRequestCache($key, $agent);
            }
        }
        return $agent;
    }

    protected function validateName($name)
    {
        if (!$name) {
            throw new LogicException('代理商员名称不能为空');
        }
        $len = mb_strlen($name);
        if ($len > 45) {
            throw new LogicException('代理商名称不能多于45个字符');
        }
    }

    protected function validateParentId($parentId)
    {
        if (!is_numeric($parentId) || intval($parentId) != $parentId || $parentId < 0) {
            throw new LogicException('上级代理商不合法');
        }

        if ($parentId != 0 && !$this->getAgent($parentId)) {
            throw new LogicException('上级代理商不存在');
        }
    }

    protected function getAgentDao()
    {
        return $this->container->get('bike.partner.dao.partner.agent');
    }
}
