<?php 
namespace Speckvisit\Specification;


class SingleDescendingOrderer extends SingleOrderer
{
	public function __construct($field)
	{
		$this->field = $field;
	}
	
	public function getDirection()
	{
		return "DESC";
	}
	
}
