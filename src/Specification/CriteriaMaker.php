<?php
namespace PhpVisitableSpecification;

class CriteriaMaker
{
   
    public function matches($field,$value)
    {
      throw new \ErrorException('dont use matches anymore.');
      //return new ComparisonCriteria("LIKE",$field,$value);
    }
    
    public function equals($field,$value)
    {
        return new EqualCriteria($field,$value);
    }
    
    public function without($field,$value)
    {
        return new NotEqualCriteria($field,$value);
    }
    
    public function greaterThan($field,$value)
    {
        return new GreaterThanCriteria($field,$value);
    }

    public function lessThan($field,$value)
    {
        return new LessThanCriteria($field,$value);
    }

    final public function greaterOrEqual($field,$value)
    {
        return new GreaterOrEqualCriteria($field,$value);
    }

    public function lessOrEqual($field,$value)
    {
        return new LessOrEqualCriteria($field,$value);
    }

    public function between($field,$start,$end)
    {
        return new CriteriaBetween($field,$start,$end);
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
    
    /*
    public function isAssociatedWith($field,$value)
    {
      return new AssociationCriteria($field,$value);
    }
    */
    
    public function any()
    {
        return new AnyCriteria();
        //return $this->without('id',-1);
    }

    public function none()
    {
        return $this->equals('id',-1);
    }
    
    public function hasId($id)
    {
        return $this->equals('id',$id);
    }
    
   
    public function belongsToUser($userId)
    {
        return $this->equals('userId',$userId);
    }
    
    public function belongsToUserId($userId)
    {
        return $this->equals('userId',$userId);
    }
        
    public function withoutOwner($userId)
    {
        return $this->without('userId',$userId);
    }
    
}
