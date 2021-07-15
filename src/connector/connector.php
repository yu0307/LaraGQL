<?php
namespace feiron\laragql\connector;

use Illuminate\Support\Arr;
use feiron\laragql\connector\contracts\connector as connectorInterface;
use feiron\laragql\connector\contracts\gqlSchemaDescriptor;
use feiron\laragql\connector\contracts\connection;
use feiron\laragql\connector\schemaDescriptor;
use Exception;

class connector implements connectorInterface
{
    protected $connection;

    protected $options;

    protected $schemaDescriptor;

    public function __construct($config=[]){
        $this->config = $config;
        $this->options = Arr::get($config, 'options', []);
        $this->config['diver']=$this->config['diver']??env('gql_driver');
        $this->connection = $this->createConnection($this->config);
        $this->schemaDescriptor = new schemaDescriptor($this->connection);
    }

    private function createConnection(){
        if(empty($this->config['diver'])){
            throw new Exception(class_basename($this).': No gql driver is specified in env file.');
        }
        else{
            $driver =(class_exists( $this->config['diver'] )?'':(__NAMESPACE__.'\\connectors\\')).$this->config['diver'];
            if ( class_exists( $driver ) ) {
                return new $driver($this->config);
            }
            throw new Exception(class_basename($this).': gql driver class cannot be found.');
        }
    }

    public function getConnection():connection{
        return $this->connection;
    }

    public function getDescriptor():gqlSchemaDescriptor{
        return $this->schemaDescriptor;
    }

    public function __call($method, $parameters){
        return call_user_func_array([$this->connection, $method], $parameters);
    }

    public function execute($query,$asArray=false){
        $this->connection->execute('{'.$query.'}',$asArray);
    }
    
}

?>