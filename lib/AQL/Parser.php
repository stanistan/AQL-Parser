<?php

	
namespace AQL;

/**
 *	Parses AQL Strings -- returns AQL Objects used to generate SQL and model data.
 */


class Parser
{
    private function _syntax_() {
        $__0__ = TRUE;
        if (($__1__ = $this->_table_definitions_()) !== NULL) {
            $__0__ = new \AQL\Query($__1__);
        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _table_definitions_() {
        $__0__ = TRUE;
        if (($__1__ = $this->_table_definition_()) !== NULL) {
            if (($__2__ = $this->_table_definitions_()) !== NULL) {
                $tmp = array_merge(array($__1__), $__2__);
                $__0__ = $tmp;
            }
            else {
                $__0__ = array($__1__);
            }

        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _table_definition_() {
        $__0__ = TRUE;
        if (($__1__ = $this->_table_declaration_()) !== NULL) {
            if ($this->_currentTokenLexeme() === '{') {
                $__2__ = $this->_currentToken();
                $this->_nextToken();
                if (($__3__ = $this->_table_contents_()) !== NULL) {
                    if ($this->_currentTokenLexeme() === '}') {
                        $__4__ = $this->_currentToken();
                        $this->_nextToken();
                        $__0__ = new \AQL\Table(array(
                        'declaration' => $__1__,
                        'body' => $__3__
                        ));
                    }
                    else {
                        $__0__ = NULL;
                    }

                }
                else {
                    $__0__ = NULL;
                }

            }
            else {
                $__0__ = NULL;
            }

        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _table_contents_() {
        $__0__ = TRUE;
        if (($__1__ = $this->_table_fields_()) !== NULL) {
            if (($__2__ = $this->_table_definitions_()) !== NULL) {
                $__0__ = array($__1__, new \AQL\Query($__2__));
            }
            else {
                $__0__ = $__1__;
            }

        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _table_declaration_() {
        $__0__ = TRUE;
        if (($__1__ = $this->_table_name_()) !== NULL) {
            if ($this->_currentTokenLexeme() === 'on') {
                $__2__ = $this->_currentToken();
                $this->_nextToken();
                if (($__3__ = $this->_table_join_defs_()) !== NULL) {
                    $__0__ = array_merge($__1__, array('joins' => $__3__ ));
                    // print_r($__0__);
                }
                else {
                    $__0__ = NULL;
                }

            }
            else {
                $__0__ = $__1__;
            }

        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _table_name_() {
        $__0__ = TRUE;
        if ($this->_currentTokenType() === self::STRING) {
            $__1__ = $this->_currentToken();
            $this->_nextToken();
            if ($this->_currentTokenLexeme() === 'as') {
                $__2__ = $this->_currentToken();
                $this->_nextToken();
                if ($this->_currentTokenType() === self::STRING) {
                    $__3__ = $this->_currentToken();
                    $this->_nextToken();
                    $__0__ = array('table_name' => $__1__->lexeme, 'table_alias' => $__3__->lexeme);
                }
                else {
                    $__0__ = NULL;
                }

            }
            else {
                $__0__ = array('table_name' => $__1__->lexeme);
            }

        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _table_join_defs_() {
        $__0__ = TRUE;
        if ($this->_currentTokenType() === self::STRING) {
            $__1__ = $this->_currentToken();
            $this->_nextToken();
            if ($this->_currentTokenLexeme() === 'and') {
                $__2__ = $this->_currentToken();
                $this->_nextToken();
                if (($__3__ = $this->_table_join_defs_()) !== NULL) {
                    $__0__ = array($__3__[0], $__1__->lexeme);
                }
                else {
                    $__0__ = NULL;
                }

            }
            else {
                $__0__ = array($__1__->lexeme);
            }

        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _table_fields_() {
        $__0__ = TRUE;
        if (($__1__ = $this->_table_contents_entry_()) !== NULL) {
            if ($this->_currentTokenLexeme() === ',') {
                $__2__ = $this->_currentToken();
                $this->_nextToken();
                if (($__3__ = $this->_table_contents_()) !== NULL) {
                    $__0__ = array($__1__, $__3__);
                }
                else {
                    $__0__ = NULL;
                }

            }
            else {
                $__0__ = $__1__;
            }

        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _table_contents_piece_() {
        $__0__ = TRUE;
        if (($__1__ = $this->_field_name_()) !== NULL) {
            $__0__ = new \AQL\Field($__1__);
        }
        else if (($__1__ = $this->_object_definition_()) !== NULL) {
            $__0__ = new \AQL\Field($__1__);
        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _table_contents_entry_() {
        $__0__ = TRUE;
        if (($__1__ = $this->_table_contents_piece_()) !== NULL) {
            if ($this->_currentTokenLexeme() === 'as') {
                $__2__ = $this->_currentToken();
                $this->_nextToken();
                if ($this->_currentTokenType() === self::STRING) {
                    $__3__ = $this->_currentToken();
                    $this->_nextToken();
                    $__1__->alias = $__3__->lexeme;
                    $__0__ = $__1__;
                }
                else {
                    $__0__ = NULL;
                }

            }
            else {
                $__0__ = $__1__;
            }

        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _object_definition_() {
        $__0__ = TRUE;
        if ($this->_currentTokenLexeme() === '[') {
            $__1__ = $this->_currentToken();
            $this->_nextToken();
            if ($this->_currentTokenType() === self::STRING) {
                $__2__ = $this->_currentToken();
                $this->_nextToken();
                if ($this->_currentTokenLexeme() === ']') {
                    $__3__ = $this->_currentToken();
                    $this->_nextToken();
                    $__0__ = array('object_name' => $__2__->lexeme);
                }
                else if ($this->_currentTokenLexeme() === '(') {
                    $__3__ = $this->_currentToken();
                    $this->_nextToken();
                    if (($__4__ = $this->_field_name_()) !== NULL) {
                        if ($this->_currentTokenLexeme() === ')') {
                            $__5__ = $this->_currentToken();
                            $this->_nextToken();
                            if ($this->_currentTokenLexeme() === ']') {
                                $__6__ = $this->_currentToken();
                                $this->_nextToken();
                                $__0__ = array(
                                'object_name' => $__2__->lexeme,
                                'constructor' => $__4__
                                );
                            }
                            else {
                                $__0__ = NULL;
                            }

                        }
                        else {
                            $__0__ = NULL;
                        }

                    }
                    else {
                        $__0__ = NULL;
                    }

                }
                else {
                    $__0__ = NULL;
                }

            }
            else {
                $__0__ = NULL;
            }

        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function _field_name_() {
        $__0__ = TRUE;
        if ($this->_currentTokenType() === self::STRING) {
            $__1__ = $this->_currentToken();
            $this->_nextToken();
            if ($this->_currentTokenLexeme() === '.') {
                $__2__ = $this->_currentToken();
                $this->_nextToken();
                if ($this->_currentTokenType() === self::STRING) {
                    $__3__ = $this->_currentToken();
                    $this->_nextToken();
                    $__0__ = array('field_name' => $__3__->lexeme, 'table' => $__1__->lexeme );
                }
                else {
                    $__0__ = NULL;
                }

            }
            else {
                $__0__ = array('field_name' => $__1__->lexeme);
            }

        }
        else {
            $__0__ = NULL;
        }
        return $__0__;
    }

    private function doParse() {
        return $this->_syntax_();
    }
    private function _currentToken() {

		return $this->token;

    }

    private function _currentTokenType() {

		return $this->token->type;

    }

    private function _currentTokenLexeme() {

		return $this->token->lexeme;

    }

    private function _nextToken() {

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
        SPECIALREGEX = '#^([\[\]{},:.()])#',
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
