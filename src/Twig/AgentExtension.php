<?php

namespace Bike\Partner\Twig;



class AgentExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('bike_parter_agent_parent', array($this, 'getParentLevelAgent')),
            new \Twig_SimpleFunction('bike_parter_agent_levelmap', array($this, 'getLevelMap')),
            new \Twig_SimpleFunction('bike_parter_agent_getone', array($this, 'getAgentById')),
        );
    }

    public function getParentLevelAgent($level)
    {
        if ($level == Agent::LEVEL_ONE) {
            return array();
        }
        $agentService = $this->container->get('bike.partner.service.agent');
        $parentAgent = $agentService->getParentAgent($level);
        if ($parentAgent) {
            return $parentAgent;
        }
        return array();

    }

    public function getLevelMap()
    {
        $agentService = $this->container->get('bike.partner.service.agent');
        $map = $agentService->getLevelMap();
        return $map;
    }

    public function getAgentById($id)
    {
        $agentService = $this->container->get('bike.partner.service.agent');
        $agent = $agentService->getAgent($id);
        return $agent;

    }

}
