<?php

namespace Bike\Partner\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use Bike\Partner\Security\User\User;

abstract class AbstractVoter extends Voter
{
    use ContainerAwareTrait;

    protected function userHasPrivilege(User $user, $subject, $action)
    {
        $privileges = $user->getPrivileges();

        return isset($privileges[$subject][$action]);
    }
}
