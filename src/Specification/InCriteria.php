<?php 
namespace Speckvisit\Specification;


class InCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitInCriteria($this);
  }
}


