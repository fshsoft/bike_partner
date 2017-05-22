<?php

namespace Bike\Partner\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Controller\AbstractController;

/**
 * @Route("/user")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/profile", name="user")
     * @Template("BikePartnerBundle:user/index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
    	$user = $this->getUser();
    	$id = $user->getId();
    	$role = strtolower(substr(strstr($user->getRole(),"_"),1));
    	$userService = $this->get('bike.partner.service.user');
    	if ($request->isMethod('post')) {
            $data = $request->request->all();
            try {
                $userService->editProfile($id,$data,$role);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        } else {
            $user = $userService->getProfile($id,$role);
            $passportService = $this->container->get('bike.partner.service.passport');
            $passport = $passportService->getPassport($id);
            return ['user'=>$user,'passport'=>$passport];
        }
        return array();
    }

    
}
