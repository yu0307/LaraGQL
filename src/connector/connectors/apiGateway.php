<?php

namespace feiron\laragql\connector\connectors;
use feiron\laragql\connector\connectors\connection;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Exception;


class apiGateway extends connection {

    protected $accessPoint;
    protected $accessToken;
    protected $serverName;

    public function __construct(array $config){
        parent::__construct($config);
        $this->readConfigs();
    }

    private function readConfigs(){
        $this->accessPoint=$this->config['accessPoint']??env('gql_accessPoint');
        $this->accessToken=$this->config['accessToken']??env('gql_accessToken');
        $this->serverName=$this->config['serverName']??env('gql_serverName');
    }

    function createConnection(array $options){
        return new Client();
    }

    public function execute($query, $asArray=false){
        try {
            $result = $this->connection->post($this->accessPoint, [
                'headers'=>[
                    'Content-Type' => 'application/json',
                    'authtoken' =>$this->accessToken
                ],
                'json' => [
                    'query'=>$query
                ]
            ]);

            if($result->getStatusCode()==200){
                return json_decode($result->getBody()->getContents(),$asArray);
            }else{
                throw new Exception(class_basename($this).$result->getBody()->getContents());
            }

        } catch (GuzzleException $e) {
            throw $e;
        }
    }
}
?>