<?php

namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;
use Bike\Partner\Db\Partner\Admin;
use Bike\Partner\Db\Partner\Passport;

class SecurityService extends AbstractService
{
	protected $roleCodeMap = array(
        Passport::TYPE_ADMIN => 'ROLE_ADMIN',
        Passport::TYPE_CS_STAFF => 'ROLE_CS_STAFF',
        Passport::TYPE_AGENT => 'ROLE_AGENT',
        Passport::TYPE_CLIENT => 'ROLE_CLIENT',
    );

    protected $roleNameMap = array(
        Passport::TYPE_ADMIN => '平台管理员',
        Passport::TYPE_CS_STAFF => '客服',
        Passport::TYPE_AGENT => '代理商',
        Passport::TYPE_CLIENT => '车主',
    );

    public function getPrivilegesByRole($role = null)
    {
        $all = $this->container->getParameter('bike.partner.params.privileges');

        if ($role === null) {
            return $all;
        }

        if (!isset($this->roleCodeMap[$role])) {
            return;
        }

        $code = $this->roleCodeMap[$role];

        $privileges = array();
        foreach ($all as $subject => $v) {
            foreach ($v['actions'] as $action => $vv) {
                if (in_array($code, $vv['roles'])) {
                    $privileges[$subject]['name'] = $v['name'];
                    $privileges[$subject]['actions'][$action] = $vv;
                }
            }
        }

        return $privileges;
    }

    public function getPrivilegeMapByRole($role = null)
    {
        $privileges = $this->getPrivilegesByRole($role);

        if (!$privileges) {
            return;
        }

        $data = array();
        foreach ($privileges as $subject => $v) {
            foreach ($v['actions'] as $action => $vv) {
                $data[$subject][$action] = $action;
            }
        }

        return $data;
    }

    public function getPrivilegeMapByPassportId($passportId)
    {
        $passportDao = $this->container->get('bike.partner.dao.partner.passport');
        $passport = $passportDao->find($passportId);
        if (!$passport) {
            return;
        }

        $privileges = $this->getPrivilegeMapByRole($passport->getType());
        if (!$privileges) {
            return;
        }

        $adminService = $this->container->get('bike.partner.service.admin');
        $adminPrivilegeList = $adminService->getAllPrivilegeListByPassportId($passportId);
        if (!$adminPrivilegeList) {
            return;
        }
        $adminPrivileges = array();
        foreach ($adminPrivilegeList as $v) {
            $adminPrivileges[$v->getSubject()][$v->getAction()] = $v->getAction();
        }

        $data = array();
        foreach ($privileges as $subject => $actions) {
            foreach ($actions as $action) {
                if (isset($adminPrivileges[$subject][$action])) {
                    $data[$subject][$action] = $action;
                }
            }
        }
        return $data;
    }

    public function getRoleCodeMap()
    {
        return $this->roleCodeMap;
    }

    public function getRoleCode($role)
    {
        if (isset($this->roleCodeMap[$role])) {
            return $this->roleCodeMap[$role];
        }
    }

    public function getRoleNameMap()
    {
        return $this->roleNameMap;
    }

    public function getRoleName($role)
    {
        if (isset($this->roleNameMap[$role])) {
            return $this->roleNameMap[$role];
        }
    }

    public function getRoleByCode($code)
    {
        return array_search($code, $this->roleCodeMap);
    }

}
