<?php 
namespace Speckvisit\Specification;

class Limiter
{
	protected $offset=0;
	protected $length=1000;

	
	
	public function __construct($offset,$length)
	{
		$this->offset = intval($offset);
		$this->length = intval($length);
	}

	public function getOffset()
	{
		return $this->offset;
	}
	
	public function getLength()
	{
		return $this->length;
	}

	public function setOffset($val)
	{
		$this->offset = $val;
	}
	
	public function setLength($val)
	{
		$this->length = $val;
	}
	
	public function getLimitClause()
	{
		return "  ".$this->getOffset().",".$this->getLength()." ";
	}
	
	public function getLimitClauseForMySql()
	{
	  return $this->getLimitClause();  
	}
	
	public function getLimitClauseForPostgreSql()
	{
	  return "  ".$this->getLength()." OFFSET ".$this->getOffset();
	}
	
	public function attachToSpecification($spec)
	{
		$spec->setLimiter($this);
	}
		

			
	public static function by($offset,$length)
	{
		return new Limiter($offset,$length);
	}
	

}

