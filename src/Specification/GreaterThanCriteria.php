<?php 
namespace PhpVisitableSpecification;


class GreaterThanCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitGreaterThanCriteria($this);
  }
}
