<?php
namespace Speckvisit\Crud\MongoDb;


class EntityIterator implements \Iterator {
    private $mongoCursor;
    private $mapper;

    public function __construct($mongoCursor, $mapper) 
    {
        $this->originalCursor = $mongoCursor;
        $this->mongoCursor =  new \IteratorIterator($mongoCursor);
        $this->mongoCursor->rewind();
        $this->mapper = $mapper;
    }

    function rewind() {
        $this->mongoCursor->rewind();
    }

    function current() {
        $currentItem = $this->mongoCursor->current();
        return $this->mapToEntity($currentItem);
    }

    function key() {
        return $this->mongoCursor->key();
    }

    function next() {
        $this->mongoCursor->next();
    }

    function valid() {
        return $this->mongoCursor->valid();
    }
    
    protected function mapToEntity($currentItem)
    {
        return $this->mapper->instantiate($currentItem);
    }
}