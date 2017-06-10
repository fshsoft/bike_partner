<?php

namespace Bike\Partner\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

use Bike\Partner\Db\Partner\Passport;

class User extends Passport implements UserInterface
{
    protected $privileges;

    protected $childs;
    protected $level;
    
    protected $roleMap = array(
        Passport::TYPE_ADMIN => 'ROLE_ADMIN',
        Passport::TYPE_CS_STAFF => 'ROLE_CS_STAFF',
        Passport::TYPE_AGENT => 'ROLE_AGENT',
        Passport::TYPE_CLIENT => 'ROLE_CLIENT',
    );

    public function getId()
    {
        return $this->getCol('id');
    }

    public function getType()
    {
        return $this->getCol('type');
    }

    public function getRole()
    {
        $type = $this->getCol('type');
        if (isset($this->roleMap[$type])) {
            return $this->roleMap[$type];
        }
    }

    public function getRoles()
    {
        $role = $this->getRole();
        if ($role) {
            return array($role);
        }
        return array();
    }

    public function getPassword()
    {
        return $this->getCol('pwd');
    }

    public function getSalt()
    {

    }

    public function getUsername()
    {
        return $this->getCol('username');
    }


    public function setPrivileges($privileges)
    {
        $this->privileges = $privileges;
    }

    public function getPrivileges()
    {
        return $this->privileges;
    }

    public function setChilds(array $childIdArray)
    {
        $this->childs = $childIdArray;
    }

    public function getChilds()
    {
        return $this->childs;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }
    public function getLevel()
    {
        return $this->level;
    }


    public function eraseCredentials()
    {

    }
}
