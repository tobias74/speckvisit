<?php 
namespace PhpVisitableSpecification;


class AnyCriteria extends AbstractCriteria
{

	final public function logicalAnd($criteria)
	{
		return $criteria;
	}

	final public function logicalOr($criteria)
	{
		return $criteria;
	}
	
	final public function logicalNot()
	{
	    throw new \ErrorException('now this does not make sense at all...');
	}
	
	public function acceptVisitor($visitor)
	{
	  $visitor->visitAnyCriteria($this);
	}
	

}
