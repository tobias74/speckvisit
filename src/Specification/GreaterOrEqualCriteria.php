<?php 
namespace PhpVisitableSpecification;


class GreaterOrEqualCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitGreaterOrEqualCriteria($this);
  }
}


