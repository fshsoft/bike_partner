<?php

namespace Bike\Partner\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RoleVoter extends AbstractVoter
{
    protected $subject = 'role';

    protected function supports($attribute, $subject)
    {
        if ($subject == $this->subject) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $attribute = (array) $attribute;
        $roles = $token->getUser()->getRoles();

        if (array_intersect($attribute, $roles)) {
            return true;
        }

        return false;
    }
}
