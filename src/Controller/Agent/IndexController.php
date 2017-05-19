<?php

namespace Bike\Partner\Controller\Agent;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Controller\AbstractController;
/**
 * @Route("/agent")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="agent")
     * @Template("BikePartnerBundle:agent/index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $agentService = $this->get('bike.partner.service.agent');
        $page = $request->query->get('p');
        $pageNum = 10;
        return $agentService->searchAgent($request->query->all(), $page, $pageNum);
    }

    /**
     * @Route("/new", name="agent_new")
     * @Template("BikePartnerBundle:agent/index:new.html.twig")
     */
    public function newAction(Request $request)
    {
        return array();
    }
}
