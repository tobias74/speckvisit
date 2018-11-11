<?php 
namespace Speckvisit\Specification;


class WithinBoundingBoxCriteria extends AbstractCriteria
{
  protected $geometryField;
  public $topLeftLatitude;
  public $topLeftLongitude;
  public $bottomRightLatitude;
  public $bottomRightLongitude;

  
  public function __construct($geometryField, $topLeftLatitude, $topLeftLongitude, $bottomRightLatitude, $bottomRightLongitude)
  {
    $this->topLeftLatitude = $topLeftLatitude;
    $this->topLeftLongitude = $topLeftLongitude;
    $this->bottomRightLatitude = $bottomRightLatitude;
    $this->bottomRightLongitude = $bottomRightLongitude;
    $this->geometryField = $geometryField;
  }
  
  public function acceptVisitor($visitor)
  {
    $visitor->visitWithinBoundingBoxCriteria($this);
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

