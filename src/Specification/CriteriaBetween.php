<?php 
namespace PhpVisitableSpecification;

class CriteriaBetween extends AbstractCriteria
{
	protected $operator;
	protected $field;
	protected $value;

	public function __construct($field,$startValue,$endValue)
	{
		$this->field = $field;
		$this->startValue = $startValue;
		$this->endValue = $endValue;
	}
	
	
  public function acceptVisitor($visitor)
  {
    $visitor->visitCriteriaBetween($this);
  }
		
	public function getField()
	{
		return $this->field;
	}
	
	public function affectsField($field)
	{
		return ($this->getField() === $field);	
	}
	
	public function getStartValue()
	{
		return $this->startValue;
	}
	
	public function getEndValue()
	{
		return $this->endValue;
	}
	
	
}
