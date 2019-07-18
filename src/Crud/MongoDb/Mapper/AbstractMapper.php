<?php

namespace Speckvisit\Crud\MongoDb\Mapper;

abstract class AbstractMapper
{
    protected $propertyList = array();

    abstract protected function transformPropertyName($property);

    public function __construct($propertyList, $collectionName, $entityProvider)
    {
        $this->propertyList = $propertyList;
        $this->collectionName = $collectionName;
        $this->entityProvider = $entityProvider;
    }

    public function getCollectionName()
    {
        return $this->collectionName;
    }

    protected function produceEntity()
    {
        return $this->entityProvider->provide();
    }

    public function getPropertyList()
    {
        return $this->propertyList;
    }

    public function getColumnForField($fieldName)
    {
        $map = $this->getMap();

        return $map[lcfirst($fieldName)];
    }

    public function instantiate($document)
    {
        $resultHash = json_decode(\MongoDB\BSON\toJSON(\MongoDB\BSON\fromPHP($document)), true);
        $entity = $this->produceEntity();
        $reflectionClass = new \ReflectionClass($entity);

        foreach ($this->getPropertyList() as $property) {
            if (isset($resultHash[$this->transformPropertyName($property)])) {
                $reflectionProperty = $reflectionClass->getProperty($property);
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($entity, $resultHash[$this->transformPropertyName($property)]);
            }
        }

        return $entity;
    }

    public function mapToDocument($entity)
    {
        $reflectionClass = new \ReflectionClass($entity);

        $document = array();
        foreach ($this->getPropertyList() as $property) {
            $reflectionProperty = $reflectionClass->getProperty($property);
            $reflectionProperty->setAccessible(true);

            $document[$this->transformPropertyName($property)] = $reflectionProperty->getValue($reflectionClass);
        }

        return $document;
    }

    protected function getMap()
    {
        $map = array();
        foreach ($this->getPropertyList() as $property) {
            $map[$property] = $this->transformPropertyName($property);
        }

        return $map;
    }
}
