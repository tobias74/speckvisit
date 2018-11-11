<?php 
namespace Speckvisit\Specification;



class LessOrEqualCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitLessOrEqualCriteria($this);
  }
}
