<?php 
namespace Speckvisit\Query;

class QueryAssembly 
{
    protected $tableNameExpression = false;
    protected $criteriaExpression = false;
    protected $limiterExpression = false;
    protected $ordererExpression = false;

    protected $resultStack = array();

    public function pushResult( $mixed ) 
    {
        array_push( $this->resultStack, $mixed );
    }

    public function popResult( ) 
    {
        return array_pop( $this->resultStack );
    }


    public function setTableNameExpression($val)
    {
        $this->tableNameExpression = $val;
    }

    public function getTableNameExpression()
    {
        return $this->tableNameExpression;
    }
    
    
    public function setCriteriaExpression($val)
    {
        $this->criteriaExpression = $val;
    }

    public function getCriteriaExpression()
    {
        return $this->criteriaExpression;
    }
    

    public function setLimiterExpression($val)
    {
        $this->limiterExpression = $val;    
    }

    public function getLimiterExpression()
    {
        return $this->limiterExpression;    
    }
    

    public function setOrdererExpression($val)
    {
        $this->ordererExpression = $val;
    }

    public function getOrdererExpression()
    {
        return $this->ordererExpression;
    }

}

