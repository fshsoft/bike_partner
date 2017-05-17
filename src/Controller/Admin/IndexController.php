<?php

namespace Bike\Partner\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Controller\AbstractController;
/**
 * @Route("/admin")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="admin_home")
     * @Template("BikePartnerBundle:admin/index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return array();
    }
}