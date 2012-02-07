<?

namespace AQL;

class Query extends \stdClass {
	
	public function __construct() {
		$args = func_get_args();
		$args = $args[0];
		// print_r($args);
		// parent::__construct($args);
		$this->tables = $args;
	}
		
}