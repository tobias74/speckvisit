<?php 
namespace PhpVisitableSpecification;




class NotEqualCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitNotEqualCriteria($this);
  }
}