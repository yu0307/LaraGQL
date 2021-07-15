<?php
namespace feiron\laragql\connector\contracts;

interface connection{

    function createConnection(array $options);

    function execute($query,$asArray=false);
}
?>