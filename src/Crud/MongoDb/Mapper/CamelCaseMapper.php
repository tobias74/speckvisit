<?php
namespace Speckvisit\Crud\MongoDb\Mapper;


class CamelCaseMapper extends AbstractMapper
{
    protected function transformPropertyName($property)
    {
        return $property;
    }


}





