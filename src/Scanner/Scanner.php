<?php 
namespace Speckvisit\Scanner;

class Scanner 
{
    protected $currentToken;

    protected $inputString;
    protected $currentInputPosition;
    
    
    public function __construct( $inputString, $assembly ) 
    {
    	$this->inputString = $inputString;
        $this->currentInputPosition = 0;
    	
        $this->assembly = $assembly;
        
        $this->currentToken = new Token(Token::START_OF_FILE);
    }
    
    public function __clone() 
    {
        $this->currentToken = clone($this->currentToken);
    }
    
    public function saveToMemento() 
    {
        $state = new ScannerMemento();
        $state->currentToken = $this->getCurrentToken();
    	$state->inputString = $this->inputString;
    	$state->currentInputPosition = $this->currentInputPosition;
        $state->assembly = clone($this->assembly);
        return $state;
    }

    public function restoreFromMemento( $state ) 
    {
        $this->currentToken = $state->currentToken;
        $this->assembly = $state->assembly;
    	$this->inputString = $state->inputString;
    	$this->currentInputPosition = $state->currentInputPosition;
    }

    public function getCurrentInputPosition()
    {
        return $this->currentInputPosition;
    }
    
    public function isAtStartOfFile()
    {
    	return ( $this->getCurrentToken()->isStartOfFile() ); 
    }

    public function isAtEndOfFile()
    {
    	return ( $this->getCurrentToken()->isEndOfFile() ); 
    }

    public function getAssembly() 
    {
        return $this->assembly;
    }
    
    public function skipWhiteSpaceTokens( ) 
    {
        while ($this->getCurrentToken()->isWhitespaceToken())
        {
            $this->proceedToNextToken();            
        }
    }

    public function getCurrentToken()
    {
    	return $this->currentToken;
    }

    public function proceedToNextToken() 
    {
    	$nextToken = new Token();
    	
    	if ($this->hasNextCharacter())
    	{
            while ( $this->hasNextCharacter() ) 
            {
                $char = $this->peekNextCharacter();
                
                if ( $this->isEolChar( $char ) ) 
                {
                    $nextToken->setValue($this->readEolCharacters());
                    $nextToken->setType(Token::END_OF_LINE);
                } 
                else if ( $this->isBeginningWordChar( $char ) ) 
                {
                	$nextToken->setValue( $this->readWord() );
                	$nextToken->setType(Token::WORD);
                } 
                else if ( $this->isBeginningNumberChar( $char ) ) 
                {
                	$nextToken->setValue($this->readNumber());
                    $nextToken->setType(Token::NUMBER);
                } 
                else if ( $this->isSpaceChar( $char ) ) 
                {
                    $nextToken->setValue($this->readSpaceCharacters());
                	$nextToken->setType(Token::WHITESPACE);
                } 
                else if ( $char === "'" ) 
                {
                	$nextToken->setValue( $char );
                	$nextToken->setType(Token::APOSTROPHY);
                    $this->proceedToNextCharacter();
                } 
                else if ( $char === '"' ) 
                {
                	$nextToken->setValue( $char );
                	$nextToken->setType(Token::QUOTE);
                    $this->proceedToNextCharacter();
                } 
                else 
                {
                	$nextToken->setValue( $char );
                    $nextToken->setType(Token::CHARACTER);
                    $this->proceedToNextCharacter();
                }

                $this->currentToken = $nextToken;
                return $this->currentToken;
            } 

    	}
    	else
    	{
            $nextToken->setValue(null);
            $nextToken->setType(Token::END_OF_FILE);
            $this->currentToken = $nextToken;
    	}
        return $this->currentToken;
    }





    private function readWord() 
    {
        $val = "";
        while ( $this->isWordChar( $this->peekNextCharacter() )) 
        {
            $char = $this->proceedToNextCharacter();
            $val .= $char;
        } 
        return $val;
    }

    private function readNumber() 
    {
        $val = "";
        while ( $this->isNumberChar( $this->peekNextCharacter() )) 
        {
            $char = $this->proceedToNextCharacter();
            $val .= $char;
        } 
        return $val;
    }
    
    private function readEolCharacters() 
    {
        $val = "";
        while ( $this->isEolChar( $this->peekNextCharacter() )) 
        {
            $char = $this->proceedToNextCharacter();
            $val .= $char;
        } 
        return $val;
    }
    
    
    private function readSpaceCharacters() 
    {
        $val = "";
        while ( $this->isSpaceChar( $this->peekNextCharacter() )) 
        {
            $char = $this->proceedToNextCharacter();
            $val .= $char;
        } 
        return $val;
    }

    private function hasNextCharacter()
    {
        return ( $this->currentInputPosition < strlen( $this->inputString ) );
    }

    private function peekNextCharacter()
    {
        $char = substr( $this->inputString, $this->currentInputPosition, 1 );
        return $char;
    }

    private function proceedToNextCharacter() 
    {
    	if ( !$this->hasNextCharacter() ) 
        {
            return false;
        }
        $char = substr( $this->inputString, $this->currentInputPosition, 1 );
        $this->currentInputPosition++;
        return $char;
    }
    
    
    private function isNumberChar( $char ) 
    {
        return preg_match( "/[0-9\.]/", $char );
    }
    
    private function isWordChar( $char ) 
    {
        return preg_match( "/[A-Za-z0-9_\-]/", $char );
    }

    private function isBeginningWordChar( $char ) 
    {
        return preg_match( "/[A-Za-z_]/", $char );
    }
    
    private function isBeginningNumberChar( $char ) 
    {
        return preg_match( "/[0-9\-]/", $char );
    }
    
    private function isSpaceChar( $char ) 
    {
        return preg_match( "/\t| /", $char );
    }

    private function isEolChar( $char ) 
    {
        return preg_match( "/\n|\r/", $char );
    }


}


