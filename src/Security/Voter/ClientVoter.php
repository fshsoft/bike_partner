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
            if (!$this->userHasPrivilege($user, $this->subject, $attribute)) {
                // return false;
            }
            return true;
        } else if ($role == 'ROLE_CS_STAFF') {
            return true;
        }
        return false;
    }
}
