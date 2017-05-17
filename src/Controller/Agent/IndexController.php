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
     * @Route("/", name="agent_home")
     * @Template("BikePartnerBundle:agent/index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return array();
    }
}
