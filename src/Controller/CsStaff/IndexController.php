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
        $csStaffService = $this->get('bike.partner.service.cs_staff');
        $page = $request->query->get('p');
        $pageNum = 10;
        return $csStaffService->searchCsStaff($request->query->all(), $page, $pageNum);
    }

    /**
     * @Route("/new", name="cs_staff_new")
     * @Template("BikePartnerBundle:cs_staff/index:new.html.twig")
     */
    public function newAction(Request $request)
    {
        return array();
    }
}
