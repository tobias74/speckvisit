<?php 
namespace Speckvisit\Specification;

interface OrdererInterface
{
	public function getOrderClause($context);
}

class AbstractOrderer
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
	
	public function chain($orderer)
	{
		return new ChainedOrderer($this, $orderer);
	}
	
	public function attachToSpecification($spec)
	{
		$spec->setOrderer($this);
	}
		
}