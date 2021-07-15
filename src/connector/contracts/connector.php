<?php
namespace feiron\laragql\connector\contracts;
use feiron\laragql\connector\contracts\gqlSchemaDescriptor;
use feiron\laragql\connector\contracts\connection;
interface connector{

    public function execute($query,$asArray=false);

    public function getConnection():connection;

    public function getDescriptor():gqlSchemaDescriptor;
}
?>