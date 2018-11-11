<?php 
namespace Speckvisit\Specification;


class GreaterThanCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitGreaterThanCriteria($this);
  }
}
