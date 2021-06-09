<?php 
namespace Speckvisit\Specification;


class ExistsCriteria extends AbstractCriteria
{
  public function __construct($field)
  {
    $this->field = $field;
  }
  
  public function acceptVisitor($visitor)
  {
    $visitor->visitExistsCriteria($this);
  }

  public function affectsField($field)
  {
    return ($this->getField() === $field);  
  }
  
  public function getField()
  {
    return $this->field;
  }
  
  
}
