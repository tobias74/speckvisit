<?php 
namespace Speckvisit\Specification;


class GreaterOrEqualCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitGreaterOrEqualCriteria($this);
  }
}


