<?php
namespace Speckvisit\Crud\MongoDb;


class UnderscoreMapper
{
    protected $propertyList = array();

    public function __construct($propertyList)
    {
        $this->propertyList = $propertyList;
    }

    public function camelCaseToUnderscore($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    public function underscoreToCamelCase($input) 
    {
        $str = str_replace('_', '', ucwords($input, '_'));
        $str = lcfirst($str);
        return $str;
    }    
    
    public function getPropertyList()
    {
        return $this->propertyList;
    }
    
    

}





