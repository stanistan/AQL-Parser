<?

namespace AQL;

class Field extends \stdClass {
	
	public function __construct() {
		$args = func_get_args();
		$args = $args[0];

		// print_r('table constructed');
	// print_r($args[0]);
		// parent::__construct($args);

		foreach ($args as $k => $v) {
			$this->$k = $v;
		}

	}

}