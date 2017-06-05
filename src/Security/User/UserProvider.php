<?php

namespace Bike\Partner\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class UserProvider implements UserProviderInterface
{
    use ContainerAwareTrait;

    public function loadUserByUsername($username)
    {
        $passportService = $this->container->get('bike.partner.service.passport');
        $passport = $passportService->getPassportByUsername($username);

        if ($passport) {
            $user = new User();
            $user->fromArray($passport->toArray());

            //privilege
            $securityService = $this->container->get('bike.partner.service.security');
            $privileges = $securityService->getPrivilegeMapByPassportId($user->getId());
            $user->setPrivileges($privileges);

            return $user;
        }

        throw new UsernameNotFoundException(sprintf('用户名 "%s" 没找到', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Bike\\Partner\\Security\\User\\User';
    }
}
