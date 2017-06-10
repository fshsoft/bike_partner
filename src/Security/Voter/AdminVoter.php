<?php

namespace Bike\Partner\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use Bike\Partner\Db\Partner\Admin;

class AdminVoter extends AbstractVoter
{
    protected $subject = 'admin';

    protected $actions = array(
        'view',
        'edit',
    );

    protected function supports($attribute, $subject)
    {
        if ($subject == $this->subject || $subject instanceof Agent) {
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
            if ($this->userHasPrivilege($user, $this->subject, $attribute)) {
                return true;
            }
            return false;
        }    
        return false;
    }
}
