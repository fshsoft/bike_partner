<?php

namespace Bike\Partner\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * @Route("/")
 */
class IndexController extends AbstractDashboardController
{
    /**
     * @Route("/", name="home")
     * @Template("BikePartnerBundle:index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/login", name="login")
     * @Template("BikePartnerBundle:index:login.html.twig")
     */
    public function loginAction(Request $request)
    {
        return array();
    }
}
