<?php
namespace Bike\Partner\Controller\Report;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Controller\AbstractController;

/**
 * @Route("/report")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="report")
     * @Template("BikePartnerBundle:report/index:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return array();
    }
}
