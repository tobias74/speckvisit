<?php 
namespace PhpVisitableSpecification;


class LessThanCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitLessThanCriteria($this);
  }
}

