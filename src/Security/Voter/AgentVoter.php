<?php

namespace Bike\Partner\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use Bike\Partner\Db\Partner\Agent;

class AgentVoter extends AbstractVoter
{
    protected $subject = 'agent';

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
            return true;
        } elseif ($role == 'ROLE_AGENT') {
            if ($subject instanceof Agent) {
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
