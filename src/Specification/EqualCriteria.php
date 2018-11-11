<?php 
namespace Speckvisit\Specification;


class EqualCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitEqualCriteria($this);
  }
}

