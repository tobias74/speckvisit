<?php

namespace Speckvisit\Crud\MongoDb;

class Repository
{
    protected $connection = false;
    protected $mapper;

    public function __construct($config, $mapper)
    {
        $this->config = $config;
        $this->mapper = $mapper;
    }

    protected function getEntityWords($words, $offset)
    {
        $commandWords = array_slice($words, $offset);
        $entityWords = array();
        $lastWord = '';
        foreach ($commandWords as $word) {
            if (('And' === $word) || ('Or' === $word)) {
                $entityWords[] = $lastWord;
                $entityWords[] = $word;
                $lastWord = '';
            } else {
                $lastWord .= $word;
            }
        }
        $entityWords[] = $lastWord;

        return $entityWords;
    }

    protected function makeSpecification($entityWords, $arguments)
    {
        if ((false !== array_search('And', $entityWords)) && (false !== array_search('Or', $entityWords))) {
            throw new \ErrorException('Error: Combined Or and And? '.print_r($entityWords, true));
        } elseif (false !== array_search('And', $entityWords)) {
            $operation = 'And';
        } elseif (false !== array_search('Or', $entityWords)) {
            $operation = 'Or';
        } else {
            // this is ok, it's just one word
        }

        $combinedWords = array_merge(array_diff($entityWords, ['And', 'Or']));

        $criteriaMaker = new \Speckvisit\Specification\CriteriaMaker();
        foreach ($combinedWords as $index => $entityWord) {
            $criteriaPart = $criteriaMaker->equals(lcfirst($combinedWords[$index]), $arguments[$index]);

            if (!isset($criteria)) {
                $criteria = $criteriaPart;
            } else {
                $command = 'logical'.$operation;
                $criteria = $criteria->$command($criteriaPart);
            }
        }

        return $criteria;
    }

    public function __call($name, $arguments)
    {
        $words = $this->splitByCamelCase($name);

        if (('get' === $words[0]) && ('By' === $words[1])) {
            $entityWords = $this->getEntityWords($words, 2);
            $criteria = $this->makeSpecification($entityWords, $arguments);

            return $this->getBySpecification($criteria);
        } elseif ('exists' === $words[0]) {
            $entityWords = $this->getEntityWords($words, 1);
            $criteria = $this->makeSpecification($entityWords, $arguments);

            return $this->existsBySpecification($criteria);
        } elseif (('get' === $words[0]) && ('One' === $words[1]) && ('By' === $words[2])) {
            $entityWords = $this->getEntityWords($words, 3);
            $criteria = $this->makeSpecification($entityWords, $arguments);

            return $this->getOneBySpecification($criteria);
        } else {
            throw new \ErrorException('Method not found '.$name);
        }
    }

    protected function splitByCamelCase($camelCaseString)
    {
        $re = '/(?<=[a-z]|[0-9])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);

        return $a;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getMapper()
    {
        return $this->mapper;
    }

    protected function getUniqueId()
    {
        $uid = uniqid();
        $uid .= rand(100000, 999999);

        return $uid;
    }

    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = new \MongoDB\Client($this->getConfig()['mongoDbHost']);
        }

        return $this->connection;
    }

    public function getMongoDbName()
    {
        return $this->getConfig()['mongoDbName'];
    }

    protected function instantiate($document)
    {
        return $this->getMapper()->instantiate($document);
    }

    public function instantiateAll($documents)
    {
        $entities = [];
        foreach ($documents as $document) {
            $entities[] = $this->instantiate($document);
        }

        return $entities;
    }

    protected function mapToDocument($entity)
    {
        return $this->getMapper()->mapToDocument($entity);
    }

    public function setIdPrefix($val)
    {
        $this->idPrefix = $val;
    }

    protected function getIdPrefix()
    {
        return $this->idPrefix;
    }

    public function merge($entity)
    {
        if (false == $entity->getId()) {
            $entity->setId($this->getIdPrefix().$this->getUniqueId());
        }

        $this->update($entity);
    }

    protected function getCollection()
    {
        $dbName = $this->getMongoDbName();
        $collectionName = $this->getMapper()->getCollectionName();
        $collection = $this->getConnection()->$dbName->$collectionName;

        return $collection;
    }

    public function update($entity)
    {
        $document = $this->mapToDocument($entity);

        $dbName = $this->getMongoDbName();
        $this->getCollection()->updateOne(array('id' => $entity->getId()), array('$set' => $document), array('upsert' => true));
    }

    public function delete($entity)
    {
        $dbName = $this->getMongoDbName();
        $this->getCollection()->deleteOne(array('id' => $entity->getId()));
    }

    public function getById($entityId)
    {
        return $this->getOneByIdAndId($entityId, $entityId);

        $criteriaMaker = new \Speckvisit\Specification\CriteriaMaker();
        $criteria = $criteriaMaker->hasId($entityId);

        return $this->getOneBySpecification($criteria);
    }

    public function getAll()
    {
        $mongoCursor = $this->getCollection()->find();
        $iterator = new EntityIterator($mongoCursor, $this->getMapper());

        return $iterator;
    }

    public function existsBySpecification($criteria)
    {
        return (bool) $this->getCollection()->findOne($this->getWhereArray($criteria));
    }

    public function getOneBySpecification($criteria)
    {
        $document = $this->getCollection()->findOne($this->getWhereArray($criteria));
        if (!$document) {
            throw new NoMatchException('not found in repository here...'.print_r($criteria, true));
        }

        return $this->instantiate($document);
    }

    public function getBySpecification($criteria)
    {
        $mongoCursor = $this->getCollection()->find($this->getWhereArray($criteria));
        $iterator = new EntityIterator($mongoCursor, $this->getMapper());

        return $iterator;
    }

    protected function getWhereArray($criteria)
    {
        $whereArrayMaker = new MongoWhereArray($this->getMapper());
        $criteria->acceptVisitor($whereArrayMaker);
        $whereArray = $whereArrayMaker->getArrayForCriteria($criteria);

        //error_log(json_encode($whereArray));

        return $whereArray;
    }
}
