<?php
namespace Speckvisit\Query\Handler;

class ComparisonHandler
{
    
	const EQUALS = "EQUALS";
	const NOT_EQUALS = "NOT_EQUALS";
	const LESSER_OR_EQUAL = "LESSER_OR_EQUAL";
	const LESSER = "LESSER";
	const GREATER_OR_EQUAL = "GREATER_OR_EQUAL";
	const GREATER = "GREATER";
    
    protected $operator;

    protected $operatorToSpecificationMap = array(
        self::EQUALS => '\Speckvisit\Specification\EqualCriteria',
        self::NOT_EQUALS => '\Speckvisit\Specification\NotEqualCriteria',
        self::LESSER_OR_EQUAL => '\\Speckvisit\Specification\LessOrEqualCriteria',
        self::LESSER => '\Speckvisit\Specification\LessThanCriteria',
        self::GREATER_OR_EQUAL => '\Speckvisit\Specification\GreaterOrEqualCriteria',
        self::GREATER => '\Speckvisit\Specification\GreaterThanCriteria',
    );

    public function __construct($operator)
    {
        $this->operator = $operator;
    }
    
    public function handleMatch( $assembly ) 
    {
        $comp1 = $assembly->popResult();
        $comp2 = $assembly->popResult();
        
        
        $reflectionObject = new \ReflectionClass($this->operatorToSpecificationMap[$this->operator]);
        $criteria = $reflectionObject->newInstanceArgs(array($comp2, $comp1) );
        
        $assembly->pushResult( $criteria );
    }
}
