<?php

namespace Bike\Partner\Controller\Bike;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Controller\AbstractController;
/**
 * @Route("/bike")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="bike")
     * @Template("BikePartnerBundle:bike/index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $bikeService = $this->get('bike.partner.service.bike');
        $page = $request->query->get('p');
        $pageNum = 10;
        return $bikeService->searchBike($request->query->all(), $page, $pageNum);
    }

    /**
     * @Route("/new", name="bike_new")
     * @Template("BikePartnerBundle:bike/index:new.html.twig")
     */
    public function newAction(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $bikeService = $this->get('bike.partner.service.bike');
                $bikeService->createBike($request->query->all());
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        }
        return array();
    }
}
