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

    protected $levelMap = array(
        Agent::LEVEL_ONE => '一级',
        Agent::LEVEL_TWO => '二级',
        Agent::LEVEL_THREE => '三级',
    );

    public function getLevelMap()
    {   
        return $this->levelMap;
    }


    public function createAgent(array $data)
    {
        $data = ArgUtil::getArgs($data, array(
            'name',
            'level',
            'username',
            'pwd',
            'repwd',
            'parent_id',
        ));
        $data['type'] = Passport::TYPE_AGENT;

        $this->validateName($data['name']);
        $this->validateLevel($data['level']);
        if (!$data['parent_id'] && Agent::LEVEL_ONE == $data['level']) {
            $data['parent_id'] = 0;
        }
        $this->validateParentId($data['parent_id']);
        if ($data['parent_id'] == 0) {
            $data['level'] = 1;
        } else {
            $agent = $this->getAgent($data['parent_id']);
            $data['level'] = $agent->getLevel() + 1;

            if ($data['level'] == Agent::LEVEL_TWO) {
                $data['parent_ids'] = ',' . $agent->getId() . ','; 
            } else {
                $data['parent_ids'] = ',' . $agent->getParentId() . ',' . $agent->getId() . ',';
            }
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

    public function editAgent($id,array $data)
    {
        $data = ArgUtil::getArgs($data, array(
            'name',
            'level',
            'username',
            'pwd',
            'repwd',
            'parent_id',
        ));
        $data['type'] = Passport::TYPE_AGENT;
        $data['id'] = $id;

        $this->validateName($data['name']);
        $this->validateLevel($data['level']);
        if (!$data['parent_id'] && Agent::LEVEL_ONE == $data['level']) {
            $data['parent_id'] = 0;
        }
        $this->validateParentId($data['parent_id']);
        if ($data['parent_id'] == 0) {
            $data['level'] = 1;
            $data['parent_ids'] = '';
        } else {
            $agent = $this->getAgent($data['parent_id']);
            $data['level'] = $agent->getLevel() + 1;

            if ($data['level'] == Agent::LEVEL_TWO) {
                $data['parent_ids'] = ',' . $agent->getId() . ','; 
            } else {
                $data['parent_ids'] = ',' . $agent->getParentId() . ',' . $agent->getId() . ',';
            }
        }
        $agentDao = $this->getAgentDao();
        $bikeDao = $this->container->get('bike.partner.dao.partner.bike');

        $oldAgent = $this->getAgent($id);
        $oldParentId = $oldAgent->getParentId();
        if ($oldParentId != $data['parent_id']) {
            switch ($oldAgent->getLevel()) {
                case Agent::LEVEL_ONE:
                    //变成二级
                    if ($data['level'] == Agent::LEVEL_TWO) {
                        $where = ['parent_ids.like'=>',' . $id . ',%','level'=>Agent::LEVEL_THREE];
                        $threeLevelAgent = $agentDao->find($where,'id');
                        if ($threeLevelAgent) {
                            throw new LogicException("目前系统只支持三级代理商,由于其下有三级代理商,不能变更等级");
                        }
                        $childIdArray = $this->getChildAgent();
                        if (!empty($childIdArray)) {
                            $agentChildChangeData = [
                                'parent_ids' => ',' . $data['parent_ids'] . ',' . $id . ',',
                                'level' => Agent::LEVEL_THREE,
                            ];
                            $agentCondition = ['agent_id.in'=>$childIdArray];

                            $allAgent = $childIdArray;
                            array_push($allAgent,$id);
                            $bikeCondition = ['agent_id.in'=>$allAgent];
                        } else {
                            $bikeCondition = ['agent_id'=>$id];
                        }
                    }
                    //变成三级
                    if ($data['level'] == Agent::LEVEL_THREE) {
                        $where = ['parent_id'=>$id];
                        $twoLevelAgent = $agentDao->find($where,'id');
                        if ($twoLevelAgent) {
                            throw new LogicException("目前系统只支持三级代理商,由于其下有二级代理商,不能变更等级");
                        }
                        $bikeCondition = ['agent_id'=>$id];
                    }
                    break;
                case Agent::LEVEL_TWO:
                    if ($data['level'] == Agent::LEVEL_THREE) {
                        $where = ['parent_id'=>$id];
                        $threeLevelAgent = $agentDao->find($where,'id');
                        if ($threeLevelAgent) {
                            throw new LogicException("目前系统只支持三级代理商,由于其下有三级代理商,不能变更等级");
                        }
                        $bikeCondition = ['agent_id'=>$id];
                    } else {
                        $childIdArray = $this->getChildAgent();
                        if (!empty($childIdArray)) {
                            $agentChildChangeData = [
                                'parent_ids' => ',' . $data['parent_ids'] . ',' . $id . ',',
                                'level' => $data['level'] + 1 ,
                            ];
                            $agentCondition = ['agent_id.in'=>$childIdArray];

                            $allAgent = $childIdArray;
                            array_push($allAgent,$id);
                            $bikeCondition = ['agent_id.in'=>$allAgent];
                        } else {
                            $bikeCondition = ['agent_id'=>$id];
                        }
                    } 
                    break;
                case Agent::LEVEL_THREE:
                    $bikeCondition = ['agent_id'=>$id];
                    break;
                default:
                    throw new LogicException("数据错误");
                    break;
            }
        }

        $passportService = $this->container->get('bike.partner.service.passport');
        $passportDao = $this->container->get('bike.partner.dao.partner.passport');

        $passportConn = $passportDao->getConn('bike.partner.dao.partner.bike');
        $agentConn = $agentDao->getConn();
        $bikeConn = $bikeDao->getConn();

        $bikeConn->beginTransaction();
        $agentConn->beginTransaction();
        $passportConn->beginTransaction();
        try {
            $passportService->updatePassport($id,$data);

            $agent = new Agent($data);
            $agentDao->save($agent);

            if ($oldParentId != $data['parent_id']) {
                if (isset($agentCondition)) {
                    $agentDao->update($agentCondition,$agentChildChangeData);
                }
                $change = ['agent_id'=>$oldParentId];
                $bikeDao->update($bikeCondition,$change);
            }
            $bikeConn->commit();
            $passportConn->commit();
            $agentConn->commit();
        } catch (\Exception $e) {
            $passportConn->rollBack();
            $agentConn->rollBack();
            $bikeConn->rollBack();
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
        $agentList = $agentDao->findList('*', $args, $offset, $pageNum);
        if ($agentList) {
            $passportIds = array();
            foreach ($agentList as $v) {
                $passportIds[] = $v->getId();
            }
            $passportDao = $this->container->get('bike.partner.dao.partner.passport');
            $passportMap = $passportDao->findMap('', array(
                'id.in' => $passportIds,
            ), 0, 0);
        } else {
            $passportMap = array();
            $agentList = array();
        }
        $total = $agentDao->findNum($args);
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


    public function getChildsAgentIdArray($agentId)
    {
        $agent = $this->getAgent($agentId);
        if (!$agent) {
            throw new LogicException("没有找到代理商");
        } 
        $idArray = [];
        $level = $agent->getLevel();
        $agentDao = $this->getAgentDao();

        if ($level == Agent::LEVEL_THREE) {
            return $idArray;
        }
        if ($level == Agent::LEVEL_TWO) {
            $where = ['parent_id'=>$agentId];
            $list = $agentDao->findList('id',$where,0,0);
            if ($list) {
                foreach ($list as $each) {
                    array_push($idArray, $each->getId());
                }
            }
            return $idArray;
        }
        if ($level == Agent::LEVEL_ONE) {
            $where = ['parent_ids.like'=>'%,' . $agentId . ',%'];
            $list2 = $agentDao->findList('id',$where,0,0);
            if ($list2) {
                foreach ($list2 as $each) {
                    array_push($idArray, $each->getId());
                }
            }
            return $idArray;
        }

    }

    public function getChildAgent($id)
    {
        $agent = $this->getAgent($id);
        $level = $agent->getLevel();
        if ($level == Agent::LEVEL_THREE) {
            return array();
        }
        $agentDao = $this->getAgentDao();
        $where = ['parent_id'=>$id];
        $agents = $agentDao->findList('id',$where,0,0);
        if ($agents) {
            $result = array();
            foreach ($agents as $each) {
                array_push($result, $each->getId());
            }
            return $result;
        }
        return array();
    }


    public function getParentAgent($level)
    {
        if ($level == Agent::LEVEL_ONE) {
            return array();
        }

        $agentDao = $this->getAgentDao();

        $where = ['level'=>$level-1];
        $agents = $agentDao->findList('id,name',$where,0,0);

        if ($agents) {
            return $agents;
        }
        return array();
    }

    public function getParentAgentIdAndNameMap($level,$id = null)
    {
        if ($level == Agent::LEVEL_ONE) {
            return array();
        }

        $agentDao = $this->getAgentDao();

        $where = ['level'=>$level-1];
        if ($id !== null) {
            $where['id.not'] = $id;
        }
        $agents = $agentDao->findList('id,name',$where,0,0);

        if ($agents) {
            $map = array();
            foreach ($agents as $each) {
                $map[$each->getId()] = $each->getName();
            }
            return $map;
        }
        return array();
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

    protected function validateLevel($level)
    {
        if (!is_numeric($level) || intval($level) != $level || $level <= 0) {
            throw new LogicException('等级不合法');
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
