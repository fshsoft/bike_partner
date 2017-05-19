<?php

namespace Bike\Partner\Controller\CsStaff;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Controller\AbstractController;
/**
 * @Route("/cs_staff")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="cs_staff")
     * @Template("BikePartnerBundle:cs_staff/index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return array();
    }
}
