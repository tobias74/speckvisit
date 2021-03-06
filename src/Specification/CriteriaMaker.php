<?php

namespace Speckvisit\Specification;

class CriteriaMaker
{
    public function equals($field, $value)
    {
        return new EqualCriteria($field, $value);
    }

    public function matches($field, $value)
    {
        return new MatchCriteria($field, $value);
    }

    public function wildcard($field, $value)
    {
        return new WildcardCriteria($field, $value);
    }

    public function terms($field, $value)
    {
        return new TermsCriteria($field, $value);
    }

    public function simpleQueryString($field, $value)
    {
        return new SimpleQueryStringCriteria($field, $value);
    }

    public function in($field, $value)
    {
        return new InCriteria($field, $value);
    }

    public function without($field, $value)
    {
        return new NotEqualCriteria($field, $value);
    }

    public function greaterThan($field, $value)
    {
        return new GreaterThanCriteria($field, $value);
    }

    public function lessThan($field, $value)
    {
        return new LessThanCriteria($field, $value);
    }

    public function greaterOrEqual($field, $value)
    {
        return new GreaterOrEqualCriteria($field, $value);
    }

    public function lessOrEqual($field, $value)
    {
        return new LessOrEqualCriteria($field, $value);
    }

    public function between($field, $start, $end)
    {
        return new CriteriaBetween($field, $start, $end);
    }

    public function withinDistance($fieldName, $pointOfInterest, $maximumDistance)
    {
        return new WithinDistanceCriteria($fieldName, $pointOfInterest, $maximumDistance);
    }

    public function withinBoundingBox($fieldName, $topLeft, $bottomRight)
    {
        return new BoundingBoxCriteria($fieldName, $topLeft, $bottomRight);
    }

    public function withinGeoHashCell($fieldName, $geoHashCell)
    {
        return new WithinGeoHashCellCriteria($fieldName, $geoHashCell);
    }

    public function geoShape($fieldName, $shapeDefinition)
    {
        return new GeoShapeCriteria($fieldName, $shapeDefinition);
    }

    public function isNotNull($field)
    {
        return new NotNullCriteria($field);
    }

    public function notNull($field)
    {
        return new NotNullCriteria($field);
    }
    
    public function exists($field)
    {
        return new ExistsCriteria($field);
    }

    public function any()
    {
        return new AnyCriteria();
        //return $this->without('id',-1);
    }

    public function none()
    {
        return $this->equals('id', -1);
    }

    public function hasId($id)
    {
        return $this->equals('id', $id);
    }
    
    public function not($criteria)
    {
        return new NotCriteria($criteria);    
    }
    
}
