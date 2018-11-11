<?php
namespace Speckvisit\Specification;



interface CriteriaInterface
{
	public function logicalAnd($criteria);
	public function logicalOr($criteria);
	public function logicalNot();
	
}
