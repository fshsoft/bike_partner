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
* @Route("/bike/revenue")
*/
class RevenueController extends AbstractController
{
	/**
	 * @Route("/",name="bike_revenue")
	 * @Template("BikePartnerBundle:bike/revenue:index.html.twig")
	 */
	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_AGENT', 'ROLE_CLIENT'), 'role');
        $this->denyAccessUnlessGranted('view', 'revenue');
        $revenueService = $this->get('bike.partner.service.bike_revenue');
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
        return $revenueService->searchBikeLog($args, $page, $pageNum);
	}

	/**
	 * @Route("/daily",name="bike_revenue_daily")
	 * @Template("BikePartnerBundle:bike/revenue:daily.html.twig")
	 */
	public function dailyAction(Request $request)
	{
		$this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_AGENT', 'ROLE_CLIENT'), 'role');
        $this->denyAccessUnlessGranted('view', 'revenue');
        $revenueService = $this->get('bike.partner.service.bike_revenue');
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
        return $revenueService->searchBikeDailyReport($args, $page, $pageNum);
	}

	/**
	 * @Route("/monthly",name="bike_revenue_monthly")
	 * @Template("BikePartnerBundle:bike/revenue:monthly.html.twig")
	 */
	public function monthlyAction(Request $request)
	{
		$this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_AGENT', 'ROLE_CLIENT'), 'role');
        $this->denyAccessUnlessGranted('view', 'revenue');
        $revenueService = $this->get('bike.partner.service.bike_revenue');
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
        return $revenueService->searchBikeMonthlyReport($args, $page, $pageNum);
	}	


    /**
     * @Route("/export/{type}", name="bike_revenue_export")
     */
    public function exportAction(Request $request,$type)
    {
        $this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_AGENT', 'ROLE_CLIENT'), 'role');
        $this->denyAccessUnlessGranted('export', 'revenue');
        try {

            $revenueService = $this->get('bike.partner.service.bike_revenue');

            $args = $request->query->all();

            $user = $this->getUser();
            $role = $user->getRole();
            if ($role == 'ROLE_AGENT') {
                $args['agent_id'] = $user->getId();
            }
            if ($role == 'ROLE_CLIENT') {
                $args['client_id'] = $user->getId();
            }
            $response = $revenueService->export($type,$args);

            return $response;
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
        return array();
    }

}
