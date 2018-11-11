<?php 
namespace Speckvisit\Specification;

class SingleOrderer extends AbstractOrderer
{
	protected $direction;
	protected $field;

	
	
	public function __construct($direction,$field)
	{
		$this->direction = $direction;
		$this->field = $field;
		
	}
	
	
	public function getField()
	{
		return $this->field;
	}
	
	public function getDirection()
	{
		return $this->direction;
	}
		
	
	
	// this has to go into the visitor
	public function getOrderClause($context)
	{
		$column = $context->getResponsibleMapperForField($this->getField())->getPreparedColumnForField($this->getField());
		$orderClause = " ".$column." ".$this->getDirection()." ";
		return $orderClause;
	}
	
	
	public function acceptVisitor($visitor)
	{
	  $visitor->visitSingleOrderer($this);
	}



	
}
