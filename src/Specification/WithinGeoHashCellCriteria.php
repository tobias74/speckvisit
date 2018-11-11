<?php 
namespace Speckvisit\Specification;


class WithinGeoHashCellCriteria extends WithinBoundingBoxCriteria
{

  public function __construct($geometryField, $geoHashCell)
  {
      list($minlng, $maxlng, $minlat, $maxlat) = \Lvht\GeoHash::::decode($geoHashCell);
      $this->topLeftLatitude = $maxlat;
      $this->topLeftLongitude = $minlon;
      $this->bottomRightLatitude = $minlat;
      $this->bottomRightLongitude = $maxlon;

      $this->geometryField = $geometryField;
  }
  
}



