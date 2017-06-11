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
        $this->denyAccessUnlessGranted(array(
            'ROLE_ADMIN', 
            'ROLE_CLIENT',
            'ROLE_CS_STAFF',
            'ROLE_AGENT',
        ), 'role');
        $this->denyAccessUnlessGranted('view','bike');
        $user = $this->getUser();
        $role = $user->getRole();
        $args = $request->query->all();
        if ($role == 'ROLE_AGENT') {
            //所有的下级
            $agentService = $this->get('bike.partner.service.agent');
            $agentIds = $agentService->getChildsAgentIdArray($user->getId());
            array_push($agentIds, $user->getId());
            $args['agent_id.in'] = $agentIds;
        } elseif ($role == 'ROLE_CLIENT') {
            $args['client_id'] = $user->getId();
        }

        $bikeService = $this->get('bike.partner.service.bike');
        $page = $request->query->get('p');
        $pageNum = 10;        

        return $bikeService->searchBike($args, $page, $pageNum);
    }

    /**
     * @Route("/new", name="bike_new")
     * @Template("BikePartnerBundle:bike/index:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN'], 'role');
        $this->denyAccessUnlessGranted('edit', 'bike');
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

    /**
     * @Route("/bind", name="bike_bind")
     */
    public function bindAction(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_CS_STAFF'], 'role');
        if ($request->isMethod('post')) {
            try {
                $id = $request->get('id','');
                $clientId = $request->get('clientId',0);
                $username = $request->get('username','');
                $bikeService = $this->get('bike.partner.service.bike');
                $bike = $bikeService->getBikeById($id);
                $this->denyAccessUnlessGranted('bind',$bike);
                $bikeService->bindBike($id,$clientId,$username);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        }
        return array();
    }

    /**
     * @Route("/unbind", name="bike_unbind")
     */
    public function unbindAction(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_CS_STAFF'], 'role');
        if ($request->isMethod('post')) {
            try {
                $id = $request->get('id');
                $bikeService = $this->get('bike.partner.service.bike');
                $bike = $bikeService->getBikeById($id);
                $this->denyAccessUnlessGranted('bind',$bike);
                $bikeService->unbindBike($id);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        }
        return array();
    }

     /**
     * @Route("/bind_agent", name="bike_bind_agent")
     */
    public function bindAgentAction(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_CS_STAFF','ROLE_AGENT'], 'role');

        if ($request->isMethod('post')) {
            try {
                $id = $request->get('id','');
                $AgentId = $request->get('agentId',0);
                $username = $request->get('username','');
                $bikeService = $this->get('bike.partner.service.bike');
                $bike = $bikeService->getBikeById($id);
                $this->denyAccessUnlessGranted('bind_agent',$bike);

                $bikeService->bindBikeAgent($id,$AgentId,$username);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        }
        return array();
    }

    /**
     * @Route("/unbind_agent", name="bike_unbind_agent")
     */
    public function unbindAgentAction(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_CS_STAFF','ROLE_AGENT'], 'role');

        if ($request->isMethod('post')) {
            try {
                $id = $request->get('id');
                $bikeService = $this->get('bike.partner.service.bike');
                $bike = $bikeService->getBikeById($id);
                $this->denyAccessUnlessGranted('bind_agent',$bike);
                $bikeService->unbindBikeAgent($id);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        }
        return array();
    }
}
