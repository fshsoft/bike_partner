<?php
namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;

class BikeRevenueService extends AbstractService
{

	public function getBikeDailyReport(array $args, $page, $pageNum)
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
        $bikeRevenueLogDao = $this->getRevenueLogByUserRole();
        $logList = $bikeRevenueLogDao->findList('sum(revenue) as revenue,log_day', $args, $offset, $pageNum, ['log_day' => 'desc'],['log_day']);
        if ($logList) {
        	$total = $bikeRevenueLogDao->findNum($args,'log_day',['log_day']);
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

	public function getBikeDailyLog(array $args, $page, $pageNum)
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
        $bikeRevenueLogDao = $this->getRevenueLogByUserRole();
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

	public function getBikeMonthlyReport(array $args, $page, $pageNum)
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
        $bikeRevenueLogDao = $this->getRevenueLogByUserRole();
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


	public function getBikeRevenueLogDao()
	{
		return $this->container->get('bike.partner.dao.partner.bike_revenue_log');
	}

    public function getRevenueLogByUserRole()
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