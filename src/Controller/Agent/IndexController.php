<?php

namespace Bike\Partner\Controller\Agent;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Controller\AbstractController;
/**
 * @Route("/agent")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="agent")
     * @Template("BikePartnerBundle:agent/index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_AGENT'), 'role');
        $this->denyAccessUnlessGranted('view', 'agent');

        $agentService = $this->get('bike.partner.service.agent');
        $page = $request->query->get('p');
        $pageNum = 10;

        $args = $request->query->all();
        $user = $this->getUser();
        if ($user->getRole() == 'ROLE_AGENT') {
            $args['parent_id'] = $user->getId();
        }

        return $agentService->searchAgent($args, $page, $pageNum);
    }

    /**
     * @Route("/new", name="agent_new")
     * @Template("BikePartnerBundle:agent/index:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_AGENT'), 'role');
        $agentService = $this->get('bike.partner.service.agent');
        if ($request->isMethod('post')) {
            $data = $request->request->all();
            try {
                $agentService->createAgent($data);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        } else {
            $agent = $agentService->getAgent($this->getUser()->getId());
            return ['agent'=>$agent];
        }
        return array();
    }


    /**
     * @Route("/edit/{id}", name="agent_edit")
     * @Template("BikePartnerBundle:agent/index:edit.html.twig")
     */
    public function editAction(Request $request,$id)
    {
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_AGENT'), 'role');
        $agentService = $this->get('bike.partner.service.agent');
        if ($request->isMethod('post')) {
            $data = $request->request->all();
            try {
                $agentService->editAgent($id,$data);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        } else {
            $agent = $agentService->getAgent($id);
            $passportService = $this->container->get('bike.partner.service.passport');
            $passport = $passportService->getPassport($id);
            return ['agent'=>$agent,'passport'=>$passport];
        }
        return array();
    }

    /**
     * @Route("/parent_agent", name="agent_parent_agent")
     */
    public function parentAgeantAction(Request $request)
    {
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_AGENT'), 'role');
        try {
            $level = $request->get('level');
            $id = $request->get('id',null);
            $agentService = $this->get('bike.partner.service.agent');
            $agents = $agentService->getParentAgentIdAndNameMap($level,$id);
            return $this->jsonSuccess($agents);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }        

    }




}
