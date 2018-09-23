<?php 
namespace PhpVisitableSpecification;



class LessOrEqualCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitLessOrEqualCriteria($this);
  }
}
