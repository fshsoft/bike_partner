<?php

namespace Bike\Partner\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Bike\Partner\Db\Partner\Passport;

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
            switch ($passport->getType()) {
                case Passport::TYPE_ADMIN:
                    $securityService = $this->container->get('bike.partner.service.security');
                    $privileges = $securityService->getPrivilegeMapByPassportId($user->getId());
                    $user->setPrivileges($privileges);
                    break;
                case Passport::TYPE_CS_STAFF:
                    $csStaffService = $this->container->get('bike.partner.service.cs_staff');
                    $csStaff = $csStaffService->getCsStaff($passport->getId());
                    $user->setLevel($csStaff->getLevel());
                    $childs = $csStaffService->getChildStaff($passport->getId());
                    $user->setChilds($childs);
                    break;
                case Passport::TYPE_AGENT:
                    $agentService = $this->container->get('bike.partner.service.agent');
                    $agent = $agentService->getAgent($passport->getId());
                    $user->setLevel($agent->getLevel());
                    $user->setChilds($agentService->getChildAgent($passport->getId()));
                    break;
                case Passport::TYPE_CLIENT:
                    break;
                default:
                    throw new AccessDeniedException("没有权限");
                    break;
            }
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
