<?php 
namespace PhpVisitableSpecification;


class EqualCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitEqualCriteria($this);
  }
}

