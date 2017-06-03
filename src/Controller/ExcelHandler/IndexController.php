<?php

namespace Bike\Partner\Controller\ExcelHandler;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

use Bike\Partner\Controller\AbstractController;
/**
 * @Route("/excelhandler")
 */
class IndexController extends AbstractController
{
	/**
     * @Route("/export/{type}", name="excel_export")
     */
    public function exportAction(Request $request,$type)
    {
    	$this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_AGENT', 'ROLE_CLIENT'), 'role');

    	try {
    		$excelHandlerService = $this->get('bike.partner.service.excel_handler');

    		$args = $request->query->all();

    		$user = $this->getUser();
    		$role = $user->getRole();
	        if ($role == 'ROLE_AGENT') {
	            $args['agent_id'] = $user->getId();
	        }
	        if ($role == 'ROLE_CLIENT') {
	        	$args['client_id'] = $user->getId();
	        }
    		$response = $excelHandlerService->export($type,$args);

    		return $response;
    	} catch (\Exception $e) {
    		return $this->jsonError($e);
    	}
        return array();
    }
	

	/**
     * @Route("/import/{type}", name="excel_import")
     * @Template("BikePartnerBundle:excelhandler:import.html.twig")
     */
    public function importAction(Request $request,$type)
    {
    	$this->denyAccessUnlessGranted(array('ROLE_ADMIN', 'ROLE_CS_STAFF'), 'role');
        if ($request->isMethod('post')) {
        	// $file = $request->files->get('file');
        	if (!isset($_FILES['file'])) {
        		throw new LogicException("文件不存在");
        	}
            $excelHandlerService = $this->get('bike.partner.service.excel_handler');
            try {	
                $excelHandlerService->import($_FILES['file']['tmp_name'], $type);
                return $this->jsonSuccess();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
        }
        return ['type'=>$type];
    }

}