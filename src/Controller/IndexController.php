<?php

namespace Bike\Partner\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Db\Partner\Passport;

/**
 * @Route("/")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @Template("BikePartnerBundle:index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/login", name="login")
     * @Template("BikePartnerBundle:index:login.html.twig")
     */
    public function loginAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/test")
     */
    public function testAction(Request $request)
    {
        $adminService = $this->get('bike.partner.service.admin');
        $data = array(
            'name' => '管理员',
            'username' => 'ethantsien',
            'pwd' => '789789',
            'repwd' => '789789',
            'type' => Passport::TYPE_ADMIN,
        );
        $adminService->createAdmin($data);
    }
}
