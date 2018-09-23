<?php
namespace PhpCrudMongo\Mapper;


class UnderscoreMapper extends AbstractMapper
{

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
    
    protected function transformPropertyName($property)
    {
        return $this->camelCaseToUnderscore($property);
    }


}





