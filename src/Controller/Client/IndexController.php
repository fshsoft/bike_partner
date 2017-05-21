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
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_CS_STAFF'), 'role');
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
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_CS_STAFF'), 'role');
        if ($request->isMethod('post')) {
            $data = $request->request->all();
            $clientService = $this->get('bike.partner.service.client');
            try {
                $clientService->createClient($data);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        }
        return array();
    }


    /**
     * @Route("/edit/{id}", name="client_edit")
     * @Template("BikePartnerBundle:client/index:edit.html.twig")
     */
    public function editAction(Request $request,$id)
    {
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_CS_STAFF'), 'role');
        $clientService = $this->get('bike.partner.service.client');
        if ($request->isMethod('post')) {
            $data = $request->request->all();
            try {
                $clientService->editClient($id,$data);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        } else {
            $client = $clientService->getClient($id);
            $passportService = $this->container->get('bike.partner.service.passport');
            $passport = $passportService->getPassport($id);
            return ['client'=>$client,'passport'=>$passport];
        }
        return array();
    }
}
