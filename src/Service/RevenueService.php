<?php
namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Exception\Logic\LogicException;
use Bike\Partner\Service\AbstractService;
use Bike\Partner\Util\ArgUtil;

class RevenueService extends AbstractService
{

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
        $bikeRevenueLogDao = $this->getBikeRevenueLogDao();
        $logList = $bikeRevenueLogDao->findList('*', $args, $offset, $pageNum, ['id' => 'desc'],['log_date']);
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


	public function getBikeRevenueLogDao()
	{
		return $this->container->get('bike.partner.dao.partner.bike_revenue_log');
	}
}