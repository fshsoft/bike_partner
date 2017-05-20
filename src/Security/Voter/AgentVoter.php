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
        $user = $token->getUser();
        if (!in_array($attribute, $this->actions)) {
            return false;
        }
        $role = $user->getRole();
        if ($role == 'ROLE_ADMIN') {
            return true;
        } elseif ($role == 'ROLE_AGENT' 
            && $subject instanceof Agent
            && in_array($user->getId(), array($subject->getId(), $subject->getParentId()))
        ) { //  如果是代理商角色，暂不考虑跨级管理
            return true;   
        }
        return false;
    }
}
