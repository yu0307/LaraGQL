<?php
namespace feiron\laragql\connector\contracts;

interface gqlSchemaDescriptor{
    function getSchemas():array;
    function describe($target);
}
?>