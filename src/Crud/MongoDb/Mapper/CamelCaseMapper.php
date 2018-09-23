<?php
namespace PhpCrudMongo\Mapper;


class CamelCaseMapper extends AbstractMapper
{
    protected function transformPropertyName($property)
    {
        return $property;
    }


}





