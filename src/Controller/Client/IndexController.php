<?php

namespace Bike\Partner\Controller\Client;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Controller\AbstractController;
/**
 * @Route("/client")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="client")
     * @Template("BikePartnerBundle:client/index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return array();
    }
}
