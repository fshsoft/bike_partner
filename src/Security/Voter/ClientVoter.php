<?php

namespace Bike\Partner\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use Bike\Partner\Db\Partner\Client;

class ClientVoter extends AbstractVoter
{
    protected $subject = 'client';

    protected $actions = array(
        'view',
        'edit',
        'new',    
    );

    protected function supports($attribute, $subject)
    {
        if ($subject == $this->subject || $subject instanceof Client) {
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
            return true;
        } elseif ($role == 'ROLE_CS_STAFF') {
            if ($subject instanceof Client) {
                if (in_array($user->getId(), array($subject->getId(), $subject->getParentId()))) {
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }
}
