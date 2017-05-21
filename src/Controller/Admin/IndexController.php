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
     * @Route("/", name="admin")
     * @Template("BikePartnerBundle:admin/index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN'), 'role');
        $adminService = $this->get('bike.partner.service.admin');
        $page = $request->query->get('p');
        $pageNum = 10;
        return $adminService->searchAdmin($request->query->all(), $page, $pageNum);
    }

    /**
     * @Route("/new", name="admin_new")
     * @Template("BikePartnerBundle:admin/index:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN'), 'role');
        if ($request->isMethod('post')) {
            $data = $request->request->all();
            $adminService = $this->get('bike.partner.service.admin');
            try {
                $adminService->createAdmin($data);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        }
        return array();
    }

    /**
     * @Route("/edit/{id}", name="admin_edit")
     * @Template("BikePartnerBundle:admin/index:edit.html.twig")
     */
    public function editAction(Request $request,$id)
    {
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN'), 'role');
        $adminService = $this->get('bike.partner.service.admin');
        if ($request->isMethod('post')) {
            $data = $request->request->all();
            try {
                $adminService->editAdmin($id,$data);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        } else {
            $admin = $adminService->getAdmin($id);
            $passportService = $this->container->get('bike.partner.service.passport');
            $passport = $passportService->getPassport($id);
            return ['admin'=>$admin,'passport'=>$passport];
        }
        return array();
    }
}
