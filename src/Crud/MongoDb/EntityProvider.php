<?php
namespace Speckvisit\Crud\MongoDb;


class EntityProvider
{

    public function __construct($className)
    {
        $this->className = $className;
    }
    
    public function provide($args = array())
    {
        $r = new \ReflectionClass($this->className);
        $obj = $r->newInstanceArgs($args);
        return $obj;
    }


}