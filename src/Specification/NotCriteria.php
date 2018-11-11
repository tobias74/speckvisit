<?php 
namespace Speckvisit\Specification;

class NotCriteria extends AbstractCriteria
{
	public function __construct($criteria)
	{
		$this->criteria = $criteria;
	}
	
  public function acceptVisitor($visitor)
  {
    $this->criteria->acceptVisitor($visitor);
    $visitor->visitNotCriteria($this);
  }
		
  public function getNestedCriteria()
  {
    return $this->criteria;  
  }
  
		
	public function affectsField($field)
	{
		return $this->criteria->affectsField($field);
	}
	
}
