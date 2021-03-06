<?php 
namespace Speckvisit\Specification;

class AndCriteria extends AbstractCriteria
{
	public function __construct($critA, $critB)
	{
		$this->critA = $critA;
		$this->critB = $critB;
	}
	
	public function acceptVisitor($visitor)
	{
	  $this->critA->acceptVisitor($visitor);
      $this->critB->acceptVisitor($visitor);
	  $visitor->visitAndCriteria($this);
	}
	
	public function getFirstCriteria()
	{
	  return $this->critA;
	}
	
	public function getSecondCriteria()
	{
	  return $this->critB;
	}
	
	public function affectsField($field)
	{
		return ($this->critA->affectsField($field) || $this->critB->affectsField($field));
	}
	
}
