<?php 
namespace Speckvisit\Specification;

abstract class ComparisonCriteria extends AbstractCriteria
{
	protected $field;
	protected $value;
	
	
	public function __construct($field,$value, $entityName = null)
	{
		$this->field = $field;
		$this->value = $value;
    $this->entityName = $entityName;
	}
	
		
	public function getValue()
	{
		return $this->value;
	}
	
	public function affectsField($field)
	{
		return ($this->getField() === $field);	
	}
	
	public function getField()
	{
		return $this->field;
	}
	
	public function getEntityName()
  {
    return $this->entityName;
  }
  
  public function hasEntityName()
  {
    return ($this->entityName != null);
  }
}

