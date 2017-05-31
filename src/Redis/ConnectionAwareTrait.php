<?php

namespace Bike\Partner\Redis;

trait ConnectionAwareTrait
{
    protected $conn;

    public function setConn(Connection $conn)
    {
        $this->conn = $conn;
        return $this;
    }
}
