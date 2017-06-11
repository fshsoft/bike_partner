<?php

namespace Bike\Partner\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use Bike\Partner\Db\Partner\Agent;
use Bike\Partner\Db\Partner\Bike;


class BikeVoter extends AbstractVoter
{
    protected $subject = 'bike';

    protected $actions = array(
        'view',
        'edit',
        'bind',
        'bind_agent',
    );

    protected function supports($attribute, $subject)
    {
        if ($subject == $this->subject || $subject instanceof Bike) {
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
            if ($subject instanceof Bike) {
                if ($user->getLevel() == Agent::LEVEL_THREE) {
                    return false;
                }
                if ($subject->getAgentId() == $user->getId() || in_array($subject->getAgentId(), $user->getChilds())) {
                    return true;
                }
                return false;
            } else {
                return true;
            }
        } elseif ($role == 'ROLE_CS_STAFF') {
            return true;
        } elseif ($role == 'ROLE_CLIENT') {
            return true;
        } 
        return false;
    }
}
