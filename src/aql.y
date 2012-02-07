grammar Parser

option (
	eol			= 	"\n";
	indentation = 	"    ";
	parse		= 	"doParse";
	algorithm	= 	"RD";	
)

@header {
	
namespace AQL;

/**
 *	Parses AQL Strings -- returns AQL Objects used to generate SQL and model data.
 */

}

@inner {
	
	private static $escapes = array(
        '\"' => '"', 
        '\\\\' => '\\',
        '\b' => "\b",
        '\f' => "\f",
        '\n' => "\n",
        '\r' => "\r",
        '\t' => "\t"
    );

    private $original_statement;
	private $statement;
	private $expression;
	private $token;

	const
        STRING = 1,
        NUMBER = 2,
        SPECIAL = 3,
        KEYWORD = 4;

    const
        SPECIALREGEX = '#^(\\{|,|\\}|\[|\\|\]|(|)|=|.])#',
        KEYWORDREGEX = '#^(true|false|null|on|as|order by|where)#i',
        STRINGREGEX = '#^([a-zA-Z_]+)#',
        NUMBERREGEX = '#^(-?(0|[1-9][0-9]*)(\\.[0-9]+([eE][+-]?[0-9]+)?)?)#';



	public static function parse($string) {
		$instance = new self;
		return $instance->_do($string);
	}

	private function _do($statement) {
		$this->original_statement = $statement;
		$this->statement = ltrim($statement);
		$this->_nextToken();
		
		try {
			$ret = $this->doParse();
		} catch (Exception $e) {
			$ret = NULL;
			throw $e;
		}	
		
		return $ret;
		
	}


}

@currentToken {
		return $this->token;
}

@currentTokenType {
		return $this->token->type;
}

@currentTokenLexeme {
		return $this->token->lexeme;
}

@nextToken {
	if (empty($this->statement)) {
		$this->token = (object) array(
			'type' => null,
			'lexeme' => null
		);
		return;
	}

	$ok = false;

	$pairs = array(
		array(self::STRING, self::STRINGREGEX),
		array(self::NUMBER, self::NUMBERREGEX),
		array(self::SPECIAL, self::SPECIALREGEX),
		array(self::KEYWORD, self::KEYWORDREGEX)
	);

	foreach ($pairs as $pair) {
		
		if (preg_match($pair[1], $this->statement, $m)) {
			
			$this->token = (object) array(
				'type' => $pair[0],
				'lexeme' => $m[1]
			);

			// print_r( PHP_EOL . var_export($this->token, true) . PHP_EOL);

			$ok = true;

			break;

		}
	}

	if (!$ok) {
		$position = strlen(str_replace($this->original_statement, '', $this->statement));

		$e = sprintf(
			'AQL Parser Error: Unexpected Token \'%s\' at character: %d ', 
			substr($this->statement, 0, 1),
			$position
		);
		throw new \Exception($e); 
	}

	// print_r($m);

	$this->statement = ltrim(substr($this->statement, strlen($m[0])));
	
}



syntax : table_definitions { $$ = new \AQL\Query($1); } ;

table_definitions
	: table_definition { $$ = array($1); }
	| table_definition table_definitions 
		{ 
			$tmp = $2;
			$tmp[] = $1;
			$$ = $tmp;
		}
	;

table_definition
	: table_declaration '{' table_contents '}' 
		{ 
			$$ = new \AQL\Table(array(
				'declaration' => $1,
				'body' => $3
			));
		}
	;

table_declaration
	: table_name	{ 	$$ = $1; 	}
	| table_name 'on' table_join_defs 
		{ 	
			$$ = array_merge($1, array('joins' => $3 )); 
			// print_r($$);
		}
	;

table_name
	: STRING 				{	$$ = array('table_name' => $1->lexeme); 	}
	| STRING 'as' STRING 	{ 	$$ = array('table_name' => $1->lexeme, 'table_alias' => $3->lexeme);  }
	;

table_join_defs
	: STRING { $$ = array($1->lexeme); }
	| STRING 'and' table_join_defs 
		{ 
			$$ = array($3[0], $1->lexeme);
		}
	;

table_contents 
	: table_contents_entry { $$ = $1; }
	| table_contents_entry ',' table_contents { $$ = array($1, $3); }
	;

table_contents_piece
	: field_name { $$ = new \AQL\Field($1); } 
	| object_definition { $$ = new \AQL\Field($1); }
	;

table_contents_entry
	: table_contents_piece { $$ = $1; }
	| table_contents_piece 'as' STRING 
		{ 
			$1->alias = $3->lexeme; 
			$$ = $1;
		}
	;

object_definition 
	: '[' STRING ']' { $$ = array('object_name' => $2->lexeme); }
	;

field_name
	: STRING { $$ = array('field_name' => $1->lexeme); }
	| STRING'.'STRING  { $$ = array('field_name' => $3->lexeme, 'table' => $1->lexeme ); }
	;

