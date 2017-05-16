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

    public function getRoles()
    {
        switch ($this->getCol('type')) {
            case Passport::ROLE_ADMIN:
                return array('ROLE_ADMIN');
            case Passport::ROLE_YUNWEI:
                return array('ROLE_YUNWEI');
            case Passport::ROLE_YIYUAN:
                return array('ROLE_YIYUAN');
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
