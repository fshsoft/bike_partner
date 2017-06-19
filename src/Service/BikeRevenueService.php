<?php
namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;

class BikeRevenueService extends AbstractService
{

    public function searchBikeLog(array $args, $page, $pageNum)
    {
        $page = intval($page);
        $pageNum = intval($pageNum);
        if ($page < 1) {
            $page = 1;
        }
        if ($pageNum < 1) {
            $pageNum = 1;
        }
        $offset = ($page - 1) * $pageNum;
        $bikeRevenueLogDao = $this->getBikeRevenueLogDao();
        $logList = $bikeRevenueLogDao->findList('*', $args, $offset, $pageNum, ['id' => 'desc']);
        if ($logList) {
            $agentIds = array();
            $clientIds = array();
            foreach ($logList as $v) {
                $agentIds[] = $v->getAgentId();
                $clientIds[] = $v->getClientId();
            }
            $agentDao = $this->container->get('bike.partner.dao.partner.agent');
            $agentMap = $agentDao->findMap('', array(
                'id.in' => $agentIds,
            ), 0, 0);
            $clientDao = $this->container->get('bike.partner.dao.partner.client');
            $clientMap = $clientDao->findMap('', array(
                'id.in' => $clientIds,
            ), 0, 0);
        } else {
            $logList = $agentMap = $clientMap = array();
        }
        $total = $bikeRevenueLogDao->findNum($args);
        if ($total) {
            $totalPage = ceil($total / $pageNum);
            if ($page > $totalPage) {
                $page = $totalPage;
            }
        } else {
            $totalPage = 1;
            $page = 1;
        }

        return array(
            'page' => $page,
            'totalPage' => $totalPage,
            'pageNum' => $pageNum,
            'total' => $total,
            'list' => array(
                'log' => $logList,
            ),
            'map' => array(
                'agent' => $agentMap,
                'client' => $clientMap,
            ),
        );

    }


	public function searchBikeDailyReport(array $args, $page, $pageNum)
	{
		$page = intval($page);
        $pageNum = intval($pageNum);
        if ($page < 1) {
            $page = 1;
        }
        if ($pageNum < 1) {
            $pageNum = 1;
        }
        $offset = ($page - 1) * $pageNum;
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $role = $user->getRole();
        switch ($role) {
            case 'ROLE_ADMIN':
                $dao = $this->container->get('bike.partner.dao.partner.bike_revenue_log');
                $logList = $dao->findList('sum(revenue) as revenue,log_day', $args, $offset, $pageNum, ['log_day' => 'desc'],['log_day']);
                if ($logList) {
                    $total = $dao->findNum($args,'log_day',['log_day']);
                } else {
                    $logList = array();
                    $total = 0;
                }
                break;
            case 'ROLE_AGENT':
                $dao = $this->container->get('bike.partner.dao.partner.agent_revenue_log');
                $logList = $dao->findList('revenue,log_day,bike_revenue,share_revenue', $args, $offset, $pageNum, ['log_day' => 'desc']);
                if ($logList) {
                    $total = $dao->findNum($args,'*');
                } else {
                    $logList = array();
                    $total = 0;
                }
                break;
            case 'ROLE_CLIENT':
                $dao = $this->container->get('bike.partner.dao.partner.client_revenue_log');
                $logList = $dao->findList('revenue,log_day', $args, $offset, $pageNum, ['log_day' => 'desc']);
                if ($logList) {
                    $total = $dao->findNum($args,'*');
                } else {
                    $logList = array();
                    $total = 0;
                }
                break;
            default:
                throw new LogicException("访问受限");
                break;
        }
        
        if ($total) {
            $totalPage = ceil($total / $pageNum);
            if ($page > $totalPage) {
                $page = $totalPage;
            }
        } else {
            $totalPage = 1;
            $page = 1;
        }

        return array(
            'page' => $page,
            'totalPage' => $totalPage,
            'pageNum' => $pageNum,
            'total' => $total,
            'list' => array(
            	'log' => $logList,
            ),
        );
	}



	public function searchBikeMonthlyReport(array $args, $page, $pageNum)
	{
		$page = intval($page);
        $pageNum = intval($pageNum);
        if ($page < 1) {
            $page = 1;
        }
        if ($pageNum < 1) {
            $pageNum = 1;
        }
        $offset = ($page - 1) * $pageNum;
        $bikeRevenueLogDao = $this->getRevenueLogDaoByUserRole();

        $logList = $bikeRevenueLogDao->findList('sum(revenue) as revenue,log_month', $args, $offset, $pageNum, ['log_month' => 'desc'],['log_month']);
        if ($logList) {
        	$total = $bikeRevenueLogDao->findNum($args,'log_month',['log_month']);
        } else {
            $logList = array();
            $total = 0;
        }
        
        if ($total) {
            $totalPage = ceil($total / $pageNum);
            if ($page > $totalPage) {
                $page = $totalPage;
            }
        } else {
            $totalPage = 1;
            $page = 1;
        }

        return array(
            'page' => $page,
            'totalPage' => $totalPage,
            'pageNum' => $pageNum,
            'total' => $total,
            'list' => array(
            	'log' => $logList,
            ),
        );	
	}

    public function export($type,$args)
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
        $excelHandlerService = $this->container->get('bike.partner.service.excel_handler');
        $response = $excelHandlerService->getResponse($fileName,$data);
        return $response;
    }


    private function getMonthProfitData(array $args)
    {   
        $result = [];
        $result['title'] = ['日期','月收益／元'];
        $result['data'] = [];

        $bikeRevenueLogDao = $this->getRevenueLogDaoByUserRole();
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

        $bikeRevenueLogDao = $this->getRevenueLogDaoByUserRole();

        $logList = $bikeRevenueLogDao->findList('sum(revenue) as revenue,log_day', $args, 0, 0, ['log_day' => 'desc'],['log_day']);
        if ($logList) {
            foreach ($logList as $log) {
                $temp = [];
                $temp[] = $log->getLogDay();
                $temp[] = $log->getRevenue();
                array_push($result['data'], $temp);
            }
        }
        return $result; 
    }


	public function getBikeRevenueLogDao()
	{
		return $this->container->get('bike.partner.dao.partner.bike_revenue_log');
	}

    public function getRevenueLogDaoByUserRole()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $role = $user->getRole();
        switch ($role) {
            case 'ROLE_ADMIN':
                $dao = $this->container->get('bike.partner.dao.partner.bike_revenue_log');
                break;
            case 'ROLE_AGENT':
                $dao = $this->container->get('bike.partner.dao.partner.agent_revenue_log');
                break;
            case 'ROLE_CLIENT':
                $dao = $this->container->get('bike.partner.dao.partner.client_revenue_log');
                break;
            default:
                throw new LogicException("访问受限");
                break;
        }
        return $dao;

    }
}
