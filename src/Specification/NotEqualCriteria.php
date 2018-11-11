<?php 
namespace Speckvisit\Specification;




class NotEqualCriteria extends ComparisonCriteria
{
  public function acceptVisitor($visitor)
  {
    $visitor->visitNotEqualCriteria($this);
  }
}