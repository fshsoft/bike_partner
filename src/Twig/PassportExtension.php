<?php

namespace Bike\Partner\Twig;

use Bike\Partner\Db\Partner\Passport;

class PassportExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('bike_partner_passport_display_name', array($this, 'getDisplayName')),
            new \Twig_SimpleFunction('bike_partner_passport_title', array($this, 'getTitle')),
        );
    }

    public function getDisplayName($id)
    {
        $passportService = $this->container->get('bike.partner.service.passport');
        $passport = $passportService->getPassport($id);
        switch ($passport->getType()) {
            case Passport::TYPE_ADMIN:
                $adminService = $this->container->get('bike.partner.service.admin');
                $admin = $adminService->getAdmin($id);
                if ($admin) {
                    return $admin->getName();
                }
                break;
            case Passport::TYPE_AGENT:
                $agentService = $this->container->get('bike.partner.service.agent');
                $agent = $agentService->getAgent($id);
                if ($agent) {
                    return $agent->getName();
                }
                break;
            case Passport::TYPE_CLIENT:
                $clientService = $this->container->get('bike.partner.service.client');
                $client = $clientService->getClient($id);
                if ($client) {
                    return $client->getName();
                }
                break;
            case Passport::TYPE_CS_STAFF:
                $csStaffService = $this->container->get('bike.partner.service.cs_staff');
                $csStaff = $csStaffService->getCsStaff($id);
                if ($csStaff) {
                    return $csStaff->getName();
                }
                break;
        } 
    }

    public function getTitle($id)
    {
        $passportService = $this->container->get('bike.partner.service.passport');
        $passport = $passportService->getPassport($id);
        switch ($passport->getType()) {
            case Passport::TYPE_ADMIN:
                $adminService = $this->container->get('bike.partner.service.admin');
                $admin = $adminService->getAdmin($id);
                if ($admin) {
                    return '管理员';
                }
                break;
            case Passport::TYPE_AGENT:
                $agentService = $this->container->get('bike.partner.service.agent');
                $agent = $agentService->getAgent($id);
                if ($agent) {
                    return '代理商';
                }
                break;
            case Passport::TYPE_CLIENT:
                $clientService = $this->container->get('bike.partner.service.client');
                $client = $clientService->getClient($id);
                if ($client) {
                    return '委托人';
                }
                break;
            case Passport::TYPE_CS_STAFF:
                $csStaffService = $this->container->get('bike.partner.service.cs_staff');
                $csStaff = $csStaffService->getCsStaff($id);
                if ($csStaff) {
                    return '客服';
                }
                break;
        }
    }
}
