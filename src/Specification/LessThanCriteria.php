<?php 
namespace Speckvisit\Specification;


class LessThanCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitLessThanCriteria($this);
  }
}

