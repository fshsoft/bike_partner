<?php

namespace Bike\Partner\Service;

use Bike\Partner\Exception\Debug\DebugException;
use Bike\Partner\Service\AbstractService;

class PassportService extends AbstractService
{
    public function getPassport($id)
    {
        $key = $this->getPassportRequestCacheKey('id', $id);
        $passport = $this->getRequestCache($key);
        if (!$passport) {
            $passportDao = $this->getPassportDao();
            $passport = $passportDao->find($id);
            if ($passport) {
                $this->setPassportRequestCache($passport);
            }
        }
        return $passport;
    }

    public function getPassportByUsername($username)
    {
        $key = $this->getPassportRequestCacheKey('username', $username);
        $passport = $this->getRequestCache($key);
        if (!$passport) {
            $passportDao = $this->getPassportDao();
            $passport = $passportDao->find(array('username' => $username));
            if ($passport) {
                $this->setPassportRequestCache($passport);
            }
        }
        return $passport;
    }

    protected function getPassportRequestCacheKey($type, $value)
    {
        switch ($type) {
            case 'id':
            case 'username':
                return 'passport.' . $type . '.' . $value;
        }
        throw new DebugException('非法的passport request cache key');
    }

    protected function getPassportDao()
    {
        return $this->container->get('bike.partner.dao.partner.passport');
    }
}
