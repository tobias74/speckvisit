<?php

namespace Speckvisit\Specification;

class MatchCriteria extends ComparisonCriteria
{
    public function acceptVisitor($visitor)
    {
        $visitor->visitMatchCriteria($this);
    }
}
