<?php

namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
class ExcelHandlerService extends AbstractService
{

	/***********导出****** >> ******/
	public function export($type,array $args)
	{
		$data = [];
		switch ($type) {
			case 'month_profit':
				$data = $this->getMonthProfitData($args);
				$fileName = 'month_profit';
				break;
			case 'daily_profit':
				$data = $this->getDailyProfitData($args);
				$fileName = 'daily_profit';
				break;
			default:
				throw new LogicException("操作失败");
				break;
		}

		$phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject();
        $phpExcelObject->getProperties()->setCreator("百宝单车")
                ->setLastModifiedBy("百宝单车")
                ->setTitle("Office 2005 XLSX Test Document")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("Test result file");

        $this->setcellValue($phpExcelObject, $data);

        $phpExcelObject->setActiveSheetIndex(0);
        $writer = $this->container->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        $response = $this->container->get('phpexcel')->createStreamedResponse($writer);

        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName . '.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

	}


	private function setcellValue($phpExcelObject, &$data)
	{
		$word = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN");
		
		$phpExcelObject->setActiveSheetIndex(0);
        $i = 0;            
		foreach ($data['title'] as $value) {
			$phpExcelObject->getActiveSheet()->setCellValue($word[$i].'1', $value,PHPExcel_Cell_DataType::TYPE_STRING);
			$i++;
		}

		$i = 2;
		foreach ($data['data'] as $each) {
			$phpExcelObject->getActiveSheet()->insertNewRowBefore($i, 1);
			$j = 0;
			foreach ($each as $value) {
				$phpExcelObject->getActiveSheet()->setCellValue($word[$j] . $i, $value,PHPExcel_Cell_DataType::TYPE_STRING);	
				$j++;
			}
			$i++;
		}

	}


	private function getMonthProfitData(array $args)
	{	
		$result = [];
		$result['title'] = ['日期','月收益／元'];
		$result['data'] = [];

		$revenueService = $this->container->get('bike.partner.service.bike_revenue');

		$bikeRevenueLogDao = $revenueService->getRevenueLogByUserRole();
        $logList = $bikeRevenueLogDao->findList('sum(revenue) as revenue,log_month', $args, 0, 0, ['log_month' => 'desc'],['log_month']);
        if ($logList) {
        	foreach ($logList as $log) {
        		$temp = [];
        		$temp[] = $log->getLogMonth();
        		$temp[] = $log->getRevenue();
        		array_push($result['data'], $temp);
        	}
        }
		return $result;
	}

	private function getDailyProfitData(array $args)
	{
		$result = [];
		$result['title'] = ['日期','日收益／元'];
		$result['data'] = [];

		$revenueService = $this->container->get('bike.partner.service.bike_revenue');

		$bikeRevenueLogDao = $revenueService->getRevenueLogByUserRole();

        $logList = $bikeRevenueLogDao->findList('sum(revenue) as revenue,log_day', $args, 0, 0, ['log_day' => 'desc'],['log_day']);
        if ($logList) {
        	foreach ($logList as $log) {
        		$temp = [];
        		$temp[] = $log->getLogDate();
        		$temp[] = $log->getRevenue();
        		array_push($result['data'], $temp);
        	}
        }
		return $result;	
	}
	/** << */





	/*********导入  暂时不用了********/
	public function import($file,$type)
	{	

		$phpExcelObject=$this->container->get('phpexcel')->createPHPExcelObject($file);

		$objWorksheet = $phpExcelObject->getActiveSheet();
		$rowNum = $objWorksheet->getHighestRow();
		$columnNum = $objWorksheet->getHighestColumn();
		$columnNum = PHPExcel_Cell::columnIndexFromString($columnNum);

		//递归输出Excel内容
		$data = array();
		for ($row = 2; $row <= $rowNum;$row++) {
			$temp = [];
			for($col = 0; $col < $columnNum; $col++)
			{
				$temp[$col] = $objWorksheet->getCellByColumnAndRow($col,$row)->getValue();
			}
			$data[$row] = $temp;
		}

		switch ($type) {
			case 'bike':
				$this->importBike($data);
				break;
			default:
				throw new LogicException("操作失败");
				break;
		}

	}


	private function importBike(&$data)
	{	
		$bikeService = $this->container->get('bike.partner.service.bike');		

		$primaryBikeDao = $bikeService->getPrimaryBikeDao();
        $primaryBikeConn = $primaryBikeDao->getConn();

        $partnerBikeDao = $bikeService->getPartnerBikeDao();
        $partnerBikeConn = $partnerBikeDao->getConn();

        $bikeIdGeneratorDao = $bikeService->getBikeIdGeneratorDao();
        $bikeIdGeneratorConn = $bikeIdGeneratorDao->getConn();

		$passportDao = $this->container->get('bike.partner.dao.partner.passport');
		$agentData = $passportDao->findList('id,username',['type'=>Passport::TYPE_AGENT],0,0);
		$agentIdUsernameMap = [];
		if ($agentData) {
			foreach ($agentData as $agent) {
				$agentIdUsernameMap[$agent->getId()] = $agent->getUsername();
			}
		}
		
		$primaryBikeConn->beginTransaction();
        $partnerBikeConn->beginTransaction();
        $bikeIdGeneratorConn->beginTransaction();
        try {
        	$primaryBikeList = [];
        	$partnerBikeList = [];

        	$count = count($data);

        	if ($count > 1000) {
        		for ($i=0; $i < ceil($count/1000); $i++) { 
        			$bikeIdArray = [];
        			$elockIdArray = [];
        			$clientUserNameArray = [];
        			for ($j=0; $j < 1000; $j++) { 
        				$offset = $j+$i*1000;
        				$each = $data[$offset];
        				if (!$each) {
        					break;
        				}
        				//拼接数据
        				$id = intval($each[0]);
        				$elockId = intval($each[1]);
        				$agentName = trim($each[2]);
        				$clientName = trim($each[3]);


			            $primaryBike = new PrimaryBike();
			            $time = time();
			            $primaryBike
			                ->setId($id)
			                ->setCreateTime($time);
			            $primaryBikeList[$id] = $primaryBike;

			            $partnerBike = new PartnerBike();
			            $partnerBike
			                ->setId($id)
			                ->setCreateTime($time);
			            $partnerBikeList[$id] = $partnerBike;
        			}
        			//校验数据	bike_id没有,  bike_id和elock_id组合不重复(导入的数据内)，elock_id有的话要存在，代理商和委托人必须存在
        			
        		}
        	} else {
        		foreach ($data as $each) {

        		}
        	}
        	//写入数据
        	
            
            $primaryBikeConn->commit();
            $partnerBikeConn->commit();
            $bikeIdGeneratorConn->commit();
        } catch (\Exception $e) {
            $primaryBikeConn->rollBack();
            $partnerBikeConn->rollBack();
            $bikeIdGeneratorConn->rollBack();
            throw $e;
        }


	}
	/** << */





}