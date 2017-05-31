<?php

namespace Bike\Partner\Redis;

use Redis as Driver;

use Bike\Partner\Exception\Debug\DebugException;

class Connection
{
    protected $host;

    protected $port;

    protected $timeout;

    protected $password;

    /**
     * @var Driver
     */
    protected $driver;

    public function __construct($host, $port, $timeout, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->password = $password;
    }

    protected function connect()
    {
        if ($this->driver === null) {
            $driver = new Driver();
            if (!$driver->connect($this->host, $this->port, $this->timeout, NULL, 100)) {
                throw new DebugException('Redis<' . $this->host . ':' . $this->port . '>无法连接');
            }

            if ($this->password) {
                $driver->auth($this->password);
            }

            $this->driver = $driver;
        }
    }

    public function __call($method, $args)
    {
        $this->connect();

        return call_user_func_array(array($this->driver, $method), $args);
    }
}
