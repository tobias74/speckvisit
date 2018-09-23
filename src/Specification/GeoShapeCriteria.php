<?php 
namespace PhpVisitableSpecification;


class GeoShapeCriteria extends AbstractCriteria
{
  protected $geometryField;
  protected $geoShapeDefinition;

  
  public function __construct($geometryField, $geoShapeDefinition)
  {
    $this->geometryField = $geometryField;
    $this->geoShapeDefinition = $geoShapeDefinition;
  }
  
  public function acceptVisitor($visitor)
  {
    $visitor->visitGeoShapeCriteria($this);
  }
    
  public function getGeoShapeDefinition()
  {
    return $this->geoShapeDefinition;  
  } 

  public function affectsField($field)
  {
    return ($this->getGeometryField() === $field);  
  }
  
  public function getField()
  {
    return $this->getGeometryField();
  }
  
  public function getGeometryField()
  {
    return $this->geometryField;
  }

}

