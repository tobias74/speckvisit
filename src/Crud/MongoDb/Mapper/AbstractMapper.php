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
        $resultHash = json_decode(\MongoDB\BSON\toJSON(\MongoDB\BSON\fromPHP($document)),true);
        $entity = $this->produceEntity();

        foreach ($this->getPropertyList() as $property)
        {
            $command = "set".ucfirst($property);
            if (isset($resultHash[ $this->transformPropertyName($property) ]))
            {
                $entity->$command($resultHash[ $this->transformPropertyName($property) ]);  
            }
        }
        return $entity;
    }
    
    public function mapToDocument($entity)
    {
        $document = array();
        foreach ($this->getPropertyList() as $property)
        {
            $command = "get".ucfirst($property);
            $document[ $this->transformPropertyName($property) ] = $entity->$command();  
        }
        return $document;
    }

    protected function getMap()
    {
        $map = array();
        foreach ($this->getPropertyList() as $property)
        {
            $map[ $property ] = $this->transformPropertyName($property); 
        }
        return $map;
    }



}