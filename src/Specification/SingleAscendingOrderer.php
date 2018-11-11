<?php 
namespace Speckvisit\Specification;


class SingleAscendingOrderer extends SingleOrderer
{
	public function __construct($field)
	{
		$this->field = $field;
	}
	
	public function getDirection()
	{
		return "ASC";
	}
	
}
