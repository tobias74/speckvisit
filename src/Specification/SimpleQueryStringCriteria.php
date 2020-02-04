<?php

namespace Speckvisit\Specification;

class SimpleQueryStringCriteria extends ComparisonCriteria
{
    public function acceptVisitor($visitor)
    {
        $visitor->visitSimpleQueryStringCriteria($this);
    }
}
