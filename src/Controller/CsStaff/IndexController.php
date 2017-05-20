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
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_CS_STAFF'), 'role');
        $this->denyAccessUnlessGranted('view', 'cs_staff');

        $csStaffService = $this->get('bike.partner.service.cs_staff');
        $page = $request->query->get('p');
        $pageNum = 10;
        $args = $request->query->all();
        $user = $this->getUser();
        if ($user->getRole() == 'ROLE_CS_STAFF') {
            $args['parent_id'] = $user->getId();
        }
        return $csStaffService->searchCsStaff($args, $page, $pageNum);
    }

    /**
     * @Route("/new", name="cs_staff_new")
     * @Template("BikePartnerBundle:cs_staff/index:new.html.twig")
     */
    public function newAction(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->request->all();
            $csStaffService = $this->get('bike.partner.service.cs_staff');
            try {
                $csStaffService->createCsStaff($data);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        }
        return array();
    }

    /**
     * @Route("/edit/{id}", name="cs_staff_edit")
     * @Template("BikePartnerBundle:cs_staff/index:edit.html.twig")
     */
    public function editAction(Request $request,$id)
    {
        $csStaffService = $this->get('bike.partner.service.cs_staff');
        if ($request->isMethod('post')) {
            $data = $request->request->all();
            try {
                $csStaffService->editCsStaff($id,$data);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        } else {
            $staff = $csStaffService->getCsStaff($id);
            $passportService = $this->container->get('bike.partner.service.passport');
            $passport = $passportService->getPassport($id);
            return ['staff'=>$staff,'passport'=>$passport];
        }
        return array();
    }

    /**
     * @Route("/parent_staff", name="cs_staff_parent_staff")
     */
    public function parentStaffAction(Request $request)
    {
        try {
            $level = $request->get('level');
            $id = $request->get('id',null);
            $csStaffService = $this->get('bike.partner.service.cs_staff');
            $staffs = $csStaffService->getParentStaffIdAndNameMap($level,$id);
            return $this->jsonSuccess($staffs);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }        

    }


}
