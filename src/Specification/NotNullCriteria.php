<?php 
namespace PhpVisitableSpecification;





class NotNullCriteria extends AbstractCriteria
{
  protected $field;
  
  public function __construct($field, $entityName = null)
  {
    $this->field = $field;
    $this->entityName = $entityName;
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
  
  public function acceptVisitor($visitor)
  {
    $visitor->visitNotNullCriteria($this);
  }
}
