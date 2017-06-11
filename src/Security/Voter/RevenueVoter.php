<?php

namespace Bike\Partner\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use Bike\Partner\Db\Partner\Agent;

class RevenueVoter extends AbstractVoter
{
    protected $subject = 'revenue';

    protected $actions = array(
        'view',
        'export',
    );

    protected function supports($attribute, $subject)
    {
        if ($subject == $this->subject) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!in_array($attribute, $this->actions)) {
            return false;
        }
        $user = $token->getUser();
        $role = $user->getRole();
        if ($role == 'ROLE_ADMIN') {
            if (!$this->userHasPrivilege($user, $this->subject, $attribute)) {
            }
            return true;
        } elseif ($role == 'ROLE_AGENT') {
            return true;
        } elseif ($role == 'ROLE_CLIENT') {
            return true;
        }   
        return false;
    }
}
