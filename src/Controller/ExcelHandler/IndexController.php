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
