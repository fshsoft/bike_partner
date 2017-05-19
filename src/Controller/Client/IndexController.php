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
        $clientService = $this->get('bike.partner.service.client');
        $page = $request->query->get('p');
        $pageNum = 10;
        return $clientService->searchClient($request->query->all(), $page, $pageNum);
    }

    /**
     * @Route("/new", name="client_new")
     * @Template("BikePartnerBundle:client/index:new.html.twig")
     */
    public function newAction(Request $request)
    {
        return array();
    }
}
