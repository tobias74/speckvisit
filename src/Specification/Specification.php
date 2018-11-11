<?php 
namespace Speckvisit\Specification;

class Specification
{
	
	protected $_criteria = false;
	protected $_orderer = false;
	protected $_limiter = false;
	
	public function __construct($criteria = false, $orderer = false, $limiter = false)
	{
		$this->_criteria = $criteria;
		$this->_orderer = $orderer;
		$this->_limiter = $limiter;
	}
	
	public function __clone()
	{
		// specifications may be cloned, nothing to do here yet.
	}
	
	
	
	
	public function affectsField($field)
	{
		return $this->_criteria->affectsField($field);
	}
	
	
	public function getOrderClause($context)
	{
		if ($this->_orderer === false)
		{
			return "";
		}
		else
		{
			return " ORDER BY ".$this->_orderer->getOrderClause($context);
		}
	}
	
	public function getLimitClause($context)
	{
		if ($this->_limiter === false)
		{
			return "";
		}
		else
		{
			return " LIMIT ".$this->_limiter->getLimitClause($context);
		}
	}


  public function getLimitClauseForMySql($context)
  {
    if ($this->_limiter === false)
    {
      return "";
    }
    else
    {
      return " LIMIT ".$this->_limiter->getLimitClauseForMySql($context);
    }
  }
	
  public function getLimitClauseForPostgreSql($context)
  {
    if ($this->_limiter === false)
    {
      return "";
    }
    else
    {
      return " LIMIT ".$this->_limiter->getLimitClauseForPostgreSql($context);
    }
  }
	
	public function hasCriteria()
	{
    return ($this->_criteria !== false);
	}	

    public function hasOrderer()
    {
    return ($this->_orderer !== false);
    }   
		
	public function getCriteria()
	{
		return $this->_criteria;
	}
	
	public function getOrderer()
	{
		return $this->_orderer;
	}
	
	public function getLimiter()
	{
		return $this->_limiter;
	}
	
	public function setCriteria($val)
	{
		$this->_criteria = $val;
	}
	
	public function setOrderer($val)
	{
		$this->_orderer = $val;
	}
	
	public function setLimiter($val)
	{
		$this->_limiter = $val;
	}
	
	public function getLimit()
	{
	  if ($this->_limiter === false)
	  {
	    return 10000;
	  }
    else 
    {
      return $this->_limiter->getLength();
    }
	}

  public function getOffset()
  {
    if ($this->_limiter === false)
    {
      return 0;
    }
    else 
    {
      return $this->_limiter->getOffset();
    }
  }
		
	public function appendLogicalAnd($criteria)
	{
		$this->setCriteria( $this->getCriteria()->logicalAnd($criteria) );
	}

	public function appendLogicalOr($criteria)
	{
		$this->setCriteria( $this->getCriteria()->logicalOr($criteria) );
	}
	
}

