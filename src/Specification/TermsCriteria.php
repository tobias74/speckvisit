<?php

namespace Speckvisit\Specification;

class TermsCriteria extends ComparisonCriteria
{
    public function acceptVisitor($visitor)
    {
        $visitor->visitTermsCriteria($this);
    }
}
