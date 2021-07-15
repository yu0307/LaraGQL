<?php
namespace feiron\laragql\query;
use feiron\laragql\connector\contracts\connector;
class builder
{
    /**
     * Custom options to add to the query.
     * @var array
     */
    public $options = [];

    protected $connector;

    public function __construct(connector $connector){
        $this->connector = $connector;
    }

    public function newQuery(){
        return new self($this->connector);
    }

    public function find($id, $columns = []){

    }

}

?>