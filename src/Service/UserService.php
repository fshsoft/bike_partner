<?php

namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;
use Bike\Partner\Db\Partner\Admin;
use Bike\Partner\Db\Partner\Agent;
use Bike\Partner\Db\Partner\Client;
use Bike\Partner\Db\Partner\CsStaff;
use Bike\Partner\Db\Partner\Passport;

class UserService extends AbstractService
{

    public function editProfile($id, array $data ,$type,$role)
    {
    	$data = ArgUtil::getArgs($data, array(
            'name',
            'username',
            'pwd',
            'repwd',
        ));
        $data['type'] = $type;
        $data['id'] = $id;
        $this->validateName($data['name']);
        $userDao = $this->getUserDao($role);
        $userConn = $userDao->getConn();
        $passportService = $this->container->get('bike.partner.service.passport');
        $passportDao = $this->container->get('bike.partner.dao.partner.passport');
        $passportConn = $passportDao->getConn();
        $userConn->beginTransaction();
        $passportConn->beginTransaction();
        try {
            $passportService->updatePassport($id,$data);
            if ($role == "admin") {
            	$user = new Admin($data);
            }elseif ($role == "agent") {
            	$user = new Agent($data);
            }elseif ($role == "cs_staff") {
             	$user = new CsStaff($data);
            }elseif ($role == "client") {
        	 	$user = new Client($data);
            }
            $userDao->save($user);
            $passportConn->commit();
            $userConn->commit();
        } catch (\Exception $e) {
            $passportConn->rollBack();
            $userConn->rollBack();
            throw $e;
        }   
    }

    public function getProfile($id,$role)
    {
    	$key = $role.".". $id;
        $user = $this->getRequestCache($key);
        if (!$user) {
            $userDao = $this->getUserDao($role);
            $user = $userDao->find($id);
            if ($user) {
                $this->setRequestCache($key, $user);
            }
        }
        return $user;      
    }

    protected function validateName($name)
    {
        if (!$name) {
            throw new LogicException('用户名称不能为空');
        }
        $len = mb_strlen($name);
        if ($len > 20) {
            throw new LogicException('用户名称不能多于20个字符');
        }
    }

    protected function getUserDao($role)
    {
        return $this->container->get('bike.partner.dao.partner.'.$role);
    }
   
}