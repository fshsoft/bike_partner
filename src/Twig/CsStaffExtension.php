<?php

namespace Bike\Partner\Twig;



class CsStaffExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('bike_parter_cs_staff_parent', array($this, 'getParentLevelStaff')),
            new \Twig_SimpleFunction('bike_parter_cs_staff_levelmap', array($this, 'getLevelMap')),
        );
    }

    public function getParentLevelStaff($level)
    {
        if ($level == CsStaff::LEVEL_ONE) {
            return array();
        }
        $csStaffService = $this->container->get('bike.partner.service.cs_staff');
        $parentStaff = $csStaffService->getParentStaff($level);
        if ($parentStaff) {
            return $parentStaff;
        }
        return array();

    }

    public function getLevelMap()
    {
        $csStaffService = $this->container->get('bike.partner.service.cs_staff');
        $map = $csStaffService->getLevelMap();
        return $map;
    }

}
