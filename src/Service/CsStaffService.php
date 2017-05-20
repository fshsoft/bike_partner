<?php

namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;
use Bike\Partner\Db\Partner\CsStaff;
use Bike\Partner\Db\Partner\Passport;

class CsStaffService extends AbstractService
{

    protected $levelMap = array(
        CsStaff::LEVEL_ONE => '一级',
        CsStaff::LEVEL_TWO => '二级',
        CsStaff::LEVEL_THREE => '三级',
    );

    public function getLevelMap()
    {   
        return $this->levelMap;
    }

    public function createCsStaff(array $data)
    {
        $data = ArgUtil::getArgs($data, array(
            'name',
            'username',
            'level',
            'pwd',
            'repwd',
            'parent_id',
        ));
        $data['type'] = Passport::TYPE_CS_STAFF;

        $this->validateName($data['name']);
        $this->validateLevel($data['level']);
        if (!$data['parent_id']&&CsStaff::LEVEL_ONE == $data['level']) {
            $data['parent_id'] = 0;
        }
        $this->validateParentId($data['parent_id']);
        if ($data['parent_id'] == 0) {
            $data['level'] = 1;
        } else {
            $csStaff = $this->getCsStaff($data['parent_id']);
            $data['level'] = $csStaff->getLevel() + 1;
        }

        $csStaffDao = $this->getCsStaffDao();
        $csStaffConn = $csStaffDao->getConn();
        $passportService = $this->container->get('bike.partner.service.passport');
        $passportDao = $this->container->get('bike.partner.dao.partner.passport');
        $passportConn = $passportDao->getConn();
        $csStaffConn->beginTransaction();
        $passportConn->beginTransaction();
        try {
            $passportId = $passportService->createPassport($data);
            $csStaff = new CsStaff($data);
            $csStaff->setId($passportId);
            $csStaffDao->create($csStaff);

            $passportConn->commit();
            $csStaffConn->commit();
        } catch (\Exception $e) {
            $passportConn->rollBack();
            $csStaffConn->rollBack();
            throw $e;
        }
    }

    public function editCsStaff($id,array $data)
    {
        $data = ArgUtil::getArgs($data, array(
            'name',
            'level',
            'username',
            'pwd',
            'repwd',
            'parent_id',
        ));
        $data['type'] = Passport::TYPE_CS_STAFF;
        $data['id'] = $id;

        $this->validateName($data['name']);
        $this->validateLevel($data['level']);
        if (!$data['parent_id']&&CsStaff::LEVEL_ONE == $data['level']) {
            $data['parent_id'] = 0;
        }
        $this->validateParentId($data['parent_id']);
        if ($data['parent_id'] == 0) {
            $data['level'] = 1;
        } else {
            $csStaff = $this->getCsStaff($data['parent_id']);
            $data['level'] = $csStaff->getLevel() + 1;
        }

        $csStaffDao = $this->getCsStaffDao();
        $csStaffConn = $csStaffDao->getConn();
        $passportService = $this->container->get('bike.partner.service.passport');
        $passportDao = $this->container->get('bike.partner.dao.partner.passport');
        $passportConn = $passportDao->getConn();
        $csStaffConn->beginTransaction();
        $passportConn->beginTransaction();
        try {

            $passportService->updatePassport($id,$data);

            $csStaff = new CsStaff($data);
            $csStaffDao->save($csStaff);

            $passportConn->commit();
            $csStaffConn->commit();
        } catch (\Exception $e) {
            $passportConn->rollBack();
            $csStaffConn->rollBack();
            throw $e;
        }
    }

    public function searchCsStaff(array $args, $page, $pageNum)
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
        $csStaffDao = $this->getCsStaffDao();
        $csStaffList = $csStaffDao->findList('*', array(), $offset, $pageNum);
        if ($csStaffList) {
            $passportIds = array();
            foreach ($csStaffList as $v) {
                $passportIds[] = $v->getId();
            }
            $passportService = $this->container->get('bike.partner.service.passport');
            $passportMap = $passportService->getPassportMapByIds('', $passportIds);
        } else {
            $passportMap = array();
            $csStaffList = array();
        }
        $total = $csStaffDao->findNum(array());
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
                'cs_staff' => $csStaffList,
            ),
            'map' => array(
                'passport' => $passportMap,
            ),
        );
    }

    public function getCsStaff($id)
    {
        $key = 'cs_staff.' . $id;
        $csStaff = $this->getRequestCache($key);
        if (!$csStaff) {
            $csStaffDao = $this->getCsStaffDao();
            $csStaff = $csStaffDao->find($id);
            if ($csStaff) {
                $this->setRequestCache($key, $csStaff);
            }
        }
        return $csStaff;
    }

    public function getParentStaff($level)
    {
        if ($level == CsStaff::LEVEL_ONE) {
            return array();
        }

        $csStaffDao = $this->getCsStaffDao();

        $where = ['level'=>$level-1];
        $staffs = $csStaffDao->findList('id,name',$where);

        if ($staffs) {
            return $staffs;
        }
        return array();
    }

    public function getParentStaffIdAndNameMap($level,$id = null)
    {
        if ($level == CsStaff::LEVEL_ONE) {
            return array();
        }

        $csStaffDao = $this->getCsStaffDao();

        $where = ['level'=>$level-1];
        if ($id !== null) {
            $where['id.not'] = $id;
        }
        $staffs = $csStaffDao->findList('id,name',$where,0,0);

        if ($staffs) {
            $map = array();
            foreach ($staffs as $each) {
                $map[$each->getId()] = $each->getName();
            }
            return $map;
        }
        return array();
    }

    protected function validateName($name)
    {
        if (!$name) {
            throw new LogicException('客服名称不能为空');
        }
        $len = mb_strlen($name);
        if ($len > 20) {
            throw new LogicException('客服名称不能多于20个字符');
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
            throw new LogicException('上级客服不合法');
        }

        if ($parentId != 0 && !$this->getCsStaff($parentId)) {
            throw new LogicException('上级客服不存在');
        }
    }

    protected function getCsStaffDao()
    {
        return $this->container->get('bike.partner.dao.partner.cs_staff');
    }
}
