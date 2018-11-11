<?php
namespace Speckvisit\Query;

use Speckvisit\Query\Handler\ComparisonHandler as ComparisonHandler;

class QueryEngine {
    private $expression;
    private $operand;
    private $interpreter;
    private $context;
    private $fieldNameIdentifier='@';
    
    protected $repositories = array();
    

    function __construct() {
    }
    
    public function setFieldNameIdentifier($char)
    {
    	$this->fieldNameIdentifier = $char;	
    }
    
    public function getFieldNameIdentifier()
    {
    	return $this->fieldNameIdentifier;
    }
    
    public function translateQueryString($queryString)
    {
    		
    	$assembly = new QueryAssembly();
        $scanner = new \Speckvisit\Scanner\Scanner( $queryString, $assembly );
        if ( $scanner->isAtStartOfFile() ) 
        {
            $scanner->proceedToNextToken();
        }
        
        $parseResult = $this->getQueryParser()->parse( $scanner ); 
         
        if ( !$parseResult->wasParsingSuccessful() || !$scanner->isAtEndOfFile() ) 
        {
            $message  = "Error in Parser at: '".substr($queryString, $scanner->getCurrentInputPosition())."'";
            throw new \Exception($message);
        }
 
        $query = $scanner->getAssembly()->popResult();
    	return $query;
    }
    
    
    public function query($queryString)
    {
		$query = $this->translateQuery($queryString);
        return $this->getRepository($query->getTableName())->getBySpecification( $query->getSpecification() );        
                        
    }

    public function addRepository($name, $repository)
    {
    	$this->repositories[$name] = $repository;
    }
    
    public function getRepository($name)
    {
    	return $this->repositories[$name];
    }	
    

    public function getQueryParser() {
        if ( ! isset( $this->expression ) ) 
        {
            $this->expression = new \Speckvisit\Parser\AlternationParser();
            $this->expression->addAlternative($this->oldGetterExpression());
            $this->expression->addAlternative($this->newExpression());
        }
        return $this->expression;

        //return $this->newExpression();
    }

    protected function newExpression()
    {
        if ( ! isset( $this->newExpression ) ) {
        	
	        $this->newExpression = new \Speckvisit\Parser\SequenceParser();
	        $this->newExpression->addToSequence( new \Speckvisit\Parser\WordParser('get') )->discard();

    		$optionalSpecificationParser = new \Speckvisit\Parser\AlternationParser();

            $withSpecificationParser = new \Speckvisit\Parser\SequenceParser();    		
	        $withSpecificationParser->addToSequence( $this->getTableAndOptionalLimitParser() );
	        $withSpecificationParser->addToSequence( $this->getSpecificationParser() );

            $withoutSpecificationParser = new \Speckvisit\Parser\SequenceParser();    		
	        $withoutSpecificationParser->addToSequence( $this->getTableAndOptionalLimitParser() );

            $optionalSpecificationParser->addAlternative($withSpecificationParser);
            $optionalSpecificationParser->addAlternative($withoutSpecificationParser);


	        $this->newExpression->addToSequence( $optionalSpecificationParser );

            $this->newExpression->setHandler(new Handler\QueryHandler());
        }
        return $this->newExpression;
    }
    
    protected function numberParser($number=null)
    {
       return new \Speckvisit\Parser\NumberParser($number);
    }

    protected function wordParser($word=null)
    {
       return new \Speckvisit\Parser\WordParser($word);
    }

    protected function getTableAndOptionalLimitParser()
    {
		$withLimitAndOffset = new \Speckvisit\Parser\SequenceParser();
		$withLimitAndOffset->setHandler(new Handler\TableWithLimitAndOffsetHandler())
		                   ->addToSequence($this->numberParser()->setHandler(new Handler\StringLiteralHandler()))
		                   ->addToSequence($this->wordParser()->setHandler( new Handler\TableNameHandler()))
		                   ->addToSequence($this->wordParser('at')->discard())
		                   ->addToSequence($this->wordParser('offset')->discard())
		                   ->addToSequence($this->numberParser()->setHandler(new Handler\StringLiteralHandler()));

        $withLimit = new \Speckvisit\Parser\SequenceParser();
		$withLimit->setHandler( new Handler\TableWithLimitHandler())
		          ->addToSequence($this->numberParser()->setHandler(new Handler\StringLiteralHandler()))
		          ->addToSequence($this->wordParser()->setHandler( new Handler\TableNameHandler()));
		
		$withoutAnything = $this->wordParser()->setHandler( new Handler\TableNameHandler() );
		
		$tableAndOptionalLimit = new \Speckvisit\Parser\AlternationParser();
		$tableAndOptionalLimit->addAlternative($withLimitAndOffset);
		$tableAndOptionalLimit->addAlternative($withLimit);
		$tableAndOptionalLimit->addAlternative($withoutAnything);

        return $tableAndOptionalLimit;        
    }
    
    protected function getSpecificationParser()
    {
        $firstSequence = new \Speckvisit\Parser\SequenceParser();
        $firstSequence->addToSequence($this->sortingExpression());
        $firstSequence->addToSequence($this->criteriaExpression());
                    
        $secondSequence = new \Speckvisit\Parser\SequenceParser();
        $secondSequence->addToSequence($this->criteriaExpression());
        $secondSequence->addToSequence($this->sortingExpression());
        

        $specificationParser = new \Speckvisit\Parser\AlternationParser();
        $specificationParser->addAlternative( $firstSequence );
        $specificationParser->addAlternative( $secondSequence );
        $specificationParser->addAlternative($this->sortingExpression());
        $specificationParser->addAlternative($this->criteriaExpression());
        
        return $specificationParser;
        
    }
    
    protected function criteriaExpression()
    {
		$whichHave = new \Speckvisit\Parser\SequenceParser();
        $whichHave->addToSequence($this->wordParser('which')->discard());	        
        $whichHave->addToSequence($this->wordParser('have')->discard());	        
		
		$criteriaIntroduction = new \Speckvisit\Parser\AlternationParser();
        $criteriaIntroduction->addAlternative( $this->wordParser('where')->discard());
        $criteriaIntroduction->addAlternative( $this->wordParser('by')->discard());
		$criteriaIntroduction->addAlternative($whichHave);

        $criteriaExpression = new \Speckvisit\Parser\SequenceParser();
        $criteriaExpression->addToSequence( $criteriaIntroduction );
        $criteriaExpression->addToSequence( $this->operand() );
        $whichbool = new \Speckvisit\Parser\AlternationParser();
        $whichbool->addAlternative( $this->orExpr() );
        $whichbool->addAlternative( $this->andExpr() );
        $bools = new \Speckvisit\Parser\RepetitionParser($whichbool);
        $criteriaExpression->addToSequence( $bools );
        
        $criteriaExpression->setHandler( new Handler\CriteriaHandler() );
		
        return $criteriaExpression;    
	}
    
    
    
    function ascendingExpression()
    {
	    $ascendingExpression = new \Speckvisit\Parser\SequenceParser();
        $ascendingExpression->addToSequence( $this->wordParser('sorted')->discard() );
        $ascendingExpression->addToSequence( $this->wordParser('by')->discard() );
        $ascendingExpression->addToSequence( $this->wordParser('ascending')->discard() );
        $ascendingExpression->addToSequence( $this->fieldNameParser() );
        $ascendingExpression->setHandler( new Handler\SortAscendingHandler() );
        
        return $ascendingExpression;
		    	
    }

    function descendingExpression()
    {
	    $descendingExpression = new \Speckvisit\Parser\SequenceParser();
        $descendingExpression->addToSequence( $this->wordParser('sorted')->discard() );
        $descendingExpression->addToSequence( $this->wordParser('by')->discard() );
        $descendingExpression->addToSequence( $this->wordParser('descending')->discard() );
        $descendingExpression->addToSequence( $this->fieldNameParser() );
        $descendingExpression->setHandler( new Handler\SortDescendingHandler() );
        return $descendingExpression;
		    	
    }
    
        
    function sortingExpression() 
    {
   		$sortingExpression = new \Speckvisit\Parser\AlternationParser();
   		$sortingExpression->addAlternative( $this->ascendingExpression() );
   		$sortingExpression->addAlternative( $this->descendingExpression() );
    		
        return $sortingExpression;
    		    	
    }
        
    
    protected function oldGetterExpression()
    {
        if ( ! isset( $this->getterExpression ) ) {
        	
	        $getter = new \Speckvisit\Parser\SequenceParser();
	        $getter->addToSequence($this->wordParser('get')->discard() );
            $getter->addToSequence($this->wordParser()->setHandler( new Handler\TableNameHandler()) ); 
	        
	        
	        $fillword = new \Speckvisit\Parser\AlternationParser();
	        $fillword->addAlternative($this->wordParser('where')->discard() );
	        $fillword->addAlternative($this->wordParser('by')->discard() );
	        
	        $whichHave = new \Speckvisit\Parser\SequenceParser();
	        $whichHave->addToSequence($this->wordParser('which')->discard());	        
	        $whichHave->addToSequence($this->wordParser('have')->discard());	        
			
			$fillword->addAlternative($whichHave);
	        	        	        
	        $getter->addToSequence( $fillword );

	        
	        
	        
        	
        	
            $this->getterExpression = new \Speckvisit\Parser\SequenceParser();
            $this->getterExpression->addToSequence( $getter );
            $this->getterExpression->addToSequence( $this->operand() );
            $whichbool = new \Speckvisit\Parser\AlternationParser();
            $whichbool->addAlternative( $this->orExpr() );
            $whichbool->addAlternative( $this->andExpr() );
            $bools = new \Speckvisit\Parser\RepetitionParser($whichbool);
            $this->getterExpression->addToSequence( $bools );
            $this->getterExpression->setHandler(new Handler\QueryHandler());
        }
        return $this->getterExpression;
    }
    
        
    
    
    
    protected function logicExpression()
    {
        if ( ! isset( $this->logicExpression ) ) {
            $this->logicExpression = new \Speckvisit\Parser\SequenceParser();
            $this->logicExpression->addToSequence( $this->operand() );
            $whichbool = new \Speckvisit\Parser\AlternationParser();
            $whichbool->addAlternative( $this->orExpr() );
            $whichbool->addAlternative( $this->andExpr() );
            $bools = new \Speckvisit\Parser\RepetitionParser($whichbool);
            $this->logicExpression->addToSequence( $bools );
        }
        return $this->logicExpression;
    }
    
    
    
    
    
    

    function orExpr() 
    {
        $or = new \Speckvisit\Parser\SequenceParser( );
        $or->addToSequence($this->wordParser('or')->discard() );
        $or->addToSequence($this->operand() );
        $or->setHandler( new Handler\BooleanOrHandler() );
        return $or;
    }

    function andExpr() 
    {
        $and = new \Speckvisit\Parser\SequenceParser();
        $and->addToSequence($this->wordParser('and')->discard() );
        $and->addToSequence( $this->operand() );
        $and->setHandler( new Handler\BooleanAndHandler() );
        return $and;
    }

    protected function characterParser($character=null)
    {
        return new \Speckvisit\Parser\CharacterParser($character);
    }

    protected function stringLiteralParser($string=null)
    {
        return new \Speckvisit\Parser\StringLiteralParser($string);
    }

    function fieldNameParser() 
    {
        $fieldName = new \Speckvisit\Parser\SequenceParser();
        $fieldName->addToSequence( $this->characterParser($this->getFieldNameIdentifier())->discard() );
        $fieldName->addToSequence( $this->wordParser()->setHandler( new Handler\FieldNameHandler() ) );
        return $fieldName;
    }

    function operand() 
    {
        if ( ! isset( $this->operand ) ) {
            $this->operand = new \Speckvisit\Parser\SequenceParser( );
            $exp = new \Speckvisit\Parser\SequenceParser( );
            $exp->addToSequence($this->characterParser( '(' )->discard());
            $exp->addToSequence($this->logicExpression());
            $exp->addToSequence($this->characterParser( ')' )->discard());
            
            $comp = new \Speckvisit\Parser\AlternationParser( );
            $comp->addAlternative( $exp ); 
            $comp->addAlternative( $this->numberParser()->setHandler( new Handler\NumberHandler()) ); 
            $comp->addAlternative( $this->stringLiteralParser()->setHandler( new Handler\StringLiteralHandler() ) ); 
            $comp->addAlternative( $this->fieldNameParser() ); 
                        
            $this->operand->addToSequence( $comp );
            $this->operand->addToSequence( new \Speckvisit\Parser\RepetitionParser( $this->comparisonExpression() ) );
        }
        return $this->operand;
    }

    
    protected function comparisonExpression()
    {
    	if ( ! isset( $this->comparisonExpression ) ) 
    	{
	    	$this->comparisonExpression = new \Speckvisit\Parser\AlternationParser();
        
            $this->comparisonExpression->addAlternative( $this->notNullExpr());
         
	    	$this->comparisonExpression->addAlternative( $this->greaterThanExpr());
	    	$this->comparisonExpression->addAlternative( $this->greaterThanOrEqualToExpr());
	    	$this->comparisonExpression->addAlternative( $this->lesserThanExpr());
	    	$this->comparisonExpression->addAlternative( $this->lesserThanOrEqualToExpr());
	    	$this->comparisonExpression->addAlternative( $this->eqExpr());
	    	$this->comparisonExpression->addAlternative( $this->notEqExpr());
        
            $this->comparisonExpression->addAlternative( $this->withinDistanceExpression());
	    			
		}
    	    	   
    	return $this->comparisonExpression; 	
    }
    
    function eqExpr() {
        $equals = new \Speckvisit\Parser\SequenceParser();
        
        $equalSign = new \Speckvisit\Parser\AlternationParser();
        $equalSign->addAlternative( $this->wordParser('equals')->discard() );
        $equalSign->addAlternative( $this->characterParser('=')->discard() );
        $equalSign->addAlternative( $this->characterParser(':')->discard() );
                        
        
        $equals->addToSequence( $equalSign );
        
        $equals->addToSequence( $this->operand() );
        $equals->setHandler( new ComparisonHandler(ComparisonHandler::EQUALS) );
        return $equals;
    }

    function notEqExpr() {
        $notEquals = new \Speckvisit\Parser\SequenceParser();
        
        $notVersionOne = new \Speckvisit\Parser\SequenceParser();
        $notVersionOne->addToSequence( $this->characterParser('!')->discard());
        $notVersionOne->addToSequence( $this->characterParser('=')->discard());

        $notVersionTwo = new \Speckvisit\Parser\SequenceParser();
        $notVersionTwo->addToSequence( $this->wordParser('is')->discard());
        $notVersionTwo->addToSequence( $this->wordParser('not')->discard());
        $notVersionTwo->addToSequence( $this->wordParser('equal')->discard());
        $notVersionTwo->addToSequence( $this->wordParser('to')->discard());
                                
        
                
        $notEqualSign = new \Speckvisit\Parser\AlternationParser();
        $notEqualSign->addAlternative( $notVersionOne )->discard();
        $notEqualSign->addAlternative( $notVersionTwo )->discard();
                        
        
        $notEquals->addToSequence( $notEqualSign );
        
        $notEquals->addToSequence( $this->operand() );
        $notEquals->setHandler( new ComparisonHandler(ComparisonHandler::NOT_EQUALS));
        return $notEquals;
    }


    function notNullExpr() {
        $notEquals = new \Speckvisit\Parser\SequenceParser();
        
        $notNullString = new \Speckvisit\Parser\SequenceParser();
        $notNullString->addToSequence($this->wordParser('is')->discard());
        $notNullString->addToSequence($this->wordParser('not')->discard());
        $notNullString->addToSequence($this->wordParser('null')->discard());
                                
        
        $notEquals->addToSequence( $notNullString );
        $notEquals->setHandler( new Handler\NotNullHandler() );
        return $notEquals;
    }

    

    function greaterThanExpr() 
    {
        $greater = new \Speckvisit\Parser\SequenceParser();
        
        $greaterThan = new \Speckvisit\Parser\SequenceParser();
        $greaterThan->addToSequence( $this->wordParser('is')->discard() );
        $greaterThan->addToSequence( $this->wordParser('greater')->discard() );
        $greaterThan->addToSequence( $this->wordParser('than')->discard() );
        
        $greaterSign = new \Speckvisit\Parser\AlternationParser();
        $greaterSign->addAlternative( $greaterThan );
        $greaterSign->addAlternative( $this->characterParser('>')->discard() );
        $greaterSign->addAlternative( $this->wordParser('gt')->discard() );
                        
        
        $greater->addToSequence( $greaterSign );
        
        $greater->addToSequence( $this->operand() );
        $greater->setHandler( new ComparisonHandler(ComparisonHandler::GREATER) );
        return $greater;
    }

    function lesserThanExpr() 
    {
        $greater = new \Speckvisit\Parser\SequenceParser();
        
        $greaterThan = new \Speckvisit\Parser\SequenceParser();
        $greaterThan->addToSequence( $this->wordParser('is')->discard() );
        $greaterThan->addToSequence( $this->wordParser('lesser')->discard() );
        $greaterThan->addToSequence( $this->wordParser('than')->discard() );
        
        $greaterSign = new \Speckvisit\Parser\AlternationParser();
        $greaterSign->addAlternative( $greaterThan );
        $greaterSign->addAlternative( $this->characterParser('<')->discard() );
        $greaterSign->addAlternative( $this->wordParser('lt')->discard() );
                        
        
        $greater->addToSequence( $greaterSign );
        
        $greater->addToSequence( $this->operand() );
        $greater->setHandler( new ComparisonHandler(ComparisonHandler::LESSER) );
        return $greater;
    }

    
    
    function greaterThanOrEqualToExpr() 
    {
        $goe = new \Speckvisit\Parser\SequenceParser();
        $goe->addToSequence( $this->characterParser('>')->discard() );
        $goe->addToSequence( $this->characterParser('=')->discard() );

        $greaterThan = new \Speckvisit\Parser\SequenceParser();
        $greaterThan->addToSequence( $this->wordParser('is')->discard() );
        $greaterThan->addToSequence( $this->wordParser('greater')->discard() );
        $greaterThan->addToSequence( $this->wordParser('than')->discard() );
        $greaterThan->addToSequence( $this->wordParser('or')->discard() );
        $greaterThan->addToSequence( $this->wordParser('equal')->discard() );
        $greaterThan->addToSequence( $this->wordParser('to')->discard() );

        $greaterSign = new \Speckvisit\Parser\AlternationParser();
        $greaterSign->addAlternative( $greaterThan );
        $greaterSign->addAlternative( $this->wordParser('gte')->discard() );
        $greaterSign->addAlternative( $goe );
                        
        
        $greater = new \Speckvisit\Parser\SequenceParser();
        $greater->addToSequence( $greaterSign );
        $greater->addToSequence( $this->operand() );
        $greater->setHandler( new ComparisonHandler(ComparisonHandler::GREATER_OR_EQUAL) );

        return $greater;
    }
            
    
    function lesserThanOrEqualToExpr() 
    {
        
        $goe = new \Speckvisit\Parser\SequenceParser();
        $goe->addToSequence( $this->characterParser('<')->discard() );
        $goe->addToSequence( $this->characterParser('=')->discard() );

        $greaterThan = new \Speckvisit\Parser\SequenceParser();
        $greaterThan->addToSequence( $this->wordParser('is')->discard() );
        $greaterThan->addToSequence( $this->wordParser('lesser')->discard() );
        $greaterThan->addToSequence( $this->wordParser('than')->discard() );
        $greaterThan->addToSequence( $this->wordParser('or')->discard() );
        $greaterThan->addToSequence( $this->wordParser('equal')->discard() );
        $greaterThan->addToSequence( $this->wordParser('to')->discard() );

        $greaterSign = new \Speckvisit\Parser\AlternationParser();
        $greaterSign->addAlternative( $greaterThan );
        $greaterSign->addAlternative( $this->wordParser('lte')->discard() );
        $greaterSign->addAlternative( $goe );
                        
        
        $greater = new \Speckvisit\Parser\SequenceParser();
        $greater->addToSequence( $greaterSign );
        $greater->addToSequence( $this->operand() );
        $greater->setHandler( new ComparisonHandler(ComparisonHandler::LESSER_OR_EQUAL) );

        return $greater;
    }
 
 
 
    function withinDistanceExpression() 
    {
      // do here
      // $queryString = "(@startLocation within_distance ('$longitude','$latitude','$range')";
      
        $stWithinDistance = new \Speckvisit\Parser\SequenceParser();
        $stWithinDistance->addToSequence( $this->wordParser('within_distance')->discard()); 
        $stWithinDistance->addToSequence( $this->characterParser( '(' )->discard()); 
        $stWithinDistance->addToSequence( $this->numberParser()->setHandler( new Handler\StringLiteralHandler()) );
        $stWithinDistance->addToSequence( $this->characterParser( ',' )->discard()); 
        $stWithinDistance->addToSequence( $this->numberParser()->setHandler( new Handler\StringLiteralHandler()) );
        $stWithinDistance->addToSequence( $this->characterParser( ',' )->discard()); 
        $stWithinDistance->addToSequence( $this->numberParser()->setHandler( new Handler\StringLiteralHandler()) );
        $stWithinDistance->addToSequence( $this->characterParser( ')' )->discard() ); 
        

                        
        
        $stWithinDistance->setHandler( new Handler\WithinDistanceHandler() );
        return $stWithinDistance;
    }
 
                
}
