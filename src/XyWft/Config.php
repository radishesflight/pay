<?php

namespace RadishesFlight\Pay\XyWft;

class Config
{
    private $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getConfig($filed)
    {
        return $this->config[$filed] ?? '';
    }
}
