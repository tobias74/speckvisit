<?php
namespace Speckvisit\Query;


class Query
{
	public function setCriteria($value)
	{
		$this->criteria = $value;
	}

	public function getCriteria()
	{
		return $this->criteria;
	}

	public function setLimiter($value)
	{
		$this->limiter = $value;
	}

	public function getLimiter()
	{
		return $this->limiter;
	}

	public function setOrderer($value)
	{
		$this->orderer = $value;
	}

	public function getOrderer()
	{
		return $this->orderer;
	}


	public function setTableName($value)
	{
		$this->tableName = $value;
	}

	public function getTableName()
	{
		return $this->tableName;
	}
	
}

?>
