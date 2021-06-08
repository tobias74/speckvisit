<?php

namespace Speckvisit\Specification;

class WildcardCriteria extends ComparisonCriteria
{
    public function acceptVisitor($visitor)
    {
        $visitor->visitWildcardCriteria($this);
    }
}
