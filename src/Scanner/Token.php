<?php 
namespace Speckvisit\Scanner;

class Token
{
	
    const NUMBER = "NUMBER";
    const CHARACTER = "CHARACTER";
	const WORD = "WORD";

    const QUOTE = "QUOTE";
    const APOSTROPHY = "APOSTROPHY";

    const WHITESPACE = "WHITESPACE";
    const END_OF_LINE = "END_OF_LINE";

    const START_OF_FILE = "START_OF_FILE";
    const END_OF_FILE = "END_OF_FILE";
	
	protected $value;
	protected $type;
	
	public function __construct( $type = null, $value=null ) 
    {
    	$this->type = $type;
        $this->value = $value;
    }
	

	public function setType($val)
	{
		$this->type=$val;
	}
		
	public function setValue($val)
	{
		$this->value=$val;
	}
		
	public function getValue()
	{
		return $this->value;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function isWhitespaceToken()
	{
		return ($this->getType() === self::WHITESPACE || $this->getType() === self::END_OF_LINE);
	}
	
	public function isWord()
	{
		return ( $this->getType() === self::WORD );
	}

    public function isNumber( ) 
    {
        return ( $this->getType() === self::NUMBER );
    }
		
	public function isQuotation()
	{
		return ( $this->getType() === self::APOSTROPHY || $this->getType() === self::QUOTE );
	}	

	public function isCharacter()
	{
		return ( $this->getType() === self::CHARACTER );
	}

	public function isEndOfLine()
	{
		return ( $this->getType() === self::END_OF_LINE );
	}

	public function isStartOfFile()
	{
		return ( $this->getType() === self::START_OF_FILE );
	}

	public function isEndOfFile()
	{
		return ( $this->getType() === self::END_OF_FILE );
	}
				
}
