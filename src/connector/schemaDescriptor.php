<?php
namespace feiron\laragql\connector;

use feiron\laragql\connector\contracts\gqlSchemaDescriptor as descriptorInterface;

class schemaDescriptor implements descriptorInterface
{
    private $conn;
    private $schemaCache=[];
    private $availableQueries=[];
    private $availableMutations=[];
    private $opNames=[];

    function __construct($connection){
        $this->conn=$connection;
        $this->getQuries();
        $this->getMutations();
        $this->buildSchema();
    }

    private function introspect($query, $asArray=true){
        return $this->conn->execute($query,$asArray);
    }

    private function buildSchema(){
        $query = "
            types {
                name
                kind
                fields{
                    name
                    type{
                        ofType{
                            name
                        }
                    }
                    description
                }
            }
        ";
        $query=$this->introspect($this->queryBuilder($query, '__schema'));
        foreach($query['data']['__schema']['types'] as $type){
            if(empty($type['kind']) || empty($type['fields']) || $type['kind']!='OBJECT' || in_array($type['name'],$this->opNames) || str_starts_with($type['name'],'__')) continue;
            else{
                $this->schemaCache[$type['name']]=[];
                foreach($type['fields'] as $field){
                    $fieldType = $field['type'];
                    $this->schemaCache[$type['name']][$field['name']]=(empty($fieldType['ofType'])?$fieldType['name']:$fieldType['ofType']['name']);
                }
            }
        }
    }

    public function getSchemas():array{
        return $this->schemaCache;
    }

    public function getMutations(){
        $query = "
            mutationType {
                fields {
                    name
                    description
                }
            }
        ";
        $query=$this->introspect($this->queryBuilder($query, '__schema'));
        if(!empty($query['data']['__schema']['mutationType'])){
            $this->opNames['query']=$query['data']['__schema']['mutationType']['name'];
            foreach($query['data']['__schema']['mutationType']['fields']??[] as $field){
                $this->availableMutations[$field['name']]=$field['description'];
            }
        }
        
    }

    public function getQuries(){
        $query = "
            queryType {
                name
                fields {
                    name
                    description
                }
            }
        ";
        $query=$this->introspect($this->queryBuilder($query, '__schema'));
        if(!empty($query['data']['__schema']['queryType'])){
            $this->opNames['query']=$query['data']['__schema']['queryType']['name'];
            foreach($query['data']['__schema']['queryType']['fields']??[] as $field){
                $this->availableQueries[$field['name']]=$field['description'];
            }
        }
    }

    private function queryBuilder($query, $type='__type'){
        $query=$type."{
            $query
        }";
        return "{ $query }";
    }

    public function describe($target){
        $query = "
            name
            kind
            fields {
                name
                description
                type {
                    name
                    kind
                    ofType {
                        name
                        kind
                    }
                }
            }
        ";
        return $this->introspect($this->queryBuilder($query, "__type(name: \"$target\")"))['data']['__type'];
    }
}