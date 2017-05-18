<?php

namespace Bike\Partner\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

use Bike\Partner\Db\Partner\Passport;

class User extends Passport implements UserInterface
{
    public function getId()
    {
        return $this->getCol('id');
    }

    public function getRoleName()
    {

    }

    public function getRoleId()
    {

    }

    public function getRoleCode()
    {

    }

    public function getRoles()
    {
        switch ($this->getCol('role')) {
            case Passport::ROLE_ADMIN:
                return array('ROLE_ADMIN');
            case Passport::ROLE_CS_STAFF:
                return array('ROLE_CS_STAFF');
            case Passport::ROLE_AGENT:
                return array('ROLE_AGENT');
            case Passport::ROLE_CLIENT:
                return array('ROLE_CLIENT');
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

    public function eraseCredentials()
    {

    }
}
