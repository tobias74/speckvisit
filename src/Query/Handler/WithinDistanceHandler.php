<?php
namespace Speckvisit\Query\Handler;

class WithinDistanceHandler
{
    function handleMatch( $assembly ) {
        $distance = $assembly->popResult();
        $latitude = $assembly->popResult();
        $longitude = $assembly->popResult();
        $geoFieldName = $assembly->popResult();

        $criteria = new \Speckvisit\Specification\WithinDistanceCriteria($geoFieldName, array('latitude'=>$latitude, 'longitude'=>$longitude), $distance);
                
        $assembly->pushResult( $criteria );
    }
}
