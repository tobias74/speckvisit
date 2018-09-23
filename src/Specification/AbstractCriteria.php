<?php 
namespace PhpVisitableSpecification;

abstract class AbstractCriteria implements CriteriaInterface
{
  private static $keycount=0;
  private $key;
    
  final public function getKey() 
  {
      if ( ! isset( $this->key ) ) 
      {
          self::$keycount++;
          $this->key=self::$keycount;
      }
      return $this->key;
  }
      
  public function logicalAnd($criteria)
  {
  	return new AndCriteria($this, $criteria);
  }
    
  public function logicalOr($criteria)
  {
  	return new OrCriteria($this, $criteria);
  }
    
  public function logicalNot()
  {
  	return new NotCriteria($this);
  }

  public function affectsField($field)
  {
      return false;  
  }
  

}
