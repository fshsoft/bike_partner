<?php
namespace Bike\Partner\Controller\Revenue;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Controller\AbstractController;

/**
* @Route("/bike_revenue")
*/
class BikeRevenueController extends AbstractController
{
	/**
	 * @Route("/",name="bike_revenue")
	 * @Template("BikePartnerBundle:revenue/bike_revenue:index.html.twig")
	 */
	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_AGENT', 'ROLE_CLIENT'), 'role');

        $revenueService = $this->get('bike.partner.service.revenue');
        $page = $request->query->get('p');
        $pageNum = 10;
        $args = $request->query->all();
        $user = $this->getUser();
        $role = $user->getRole();
        if ($role == 'ROLE_CLIENT') {
            $args['client_id'] = $user->getId();
        } else if ($role == 'ROLE_AGENT') {
        	$args['agent_id'] = $user->getId();
        }
        return $revenueService->getBikeDailyLog($args, $page, $pageNum);
	}
	
}
