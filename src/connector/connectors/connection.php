<?php

namespace feiron\laragql\connector\connectors;
use Illuminate\Support\Arr;
use feiron\laragql\connector\contracts\connection as connectionInterface;

abstract class connection implements connectionInterface {

    protected $config;
    protected $options;
    protected $connection;

    public function __construct(array $config){
        $this->config=$config;
        $this->options = Arr::get($config, 'options', []);
        $this->connection = $this->createConnection($this->options);
    }

    abstract public function execute($query,$asArray=false);

    abstract function createConnection(array $options);
}
?>