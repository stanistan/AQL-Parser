<?

include 'lib/SplClassLoader.php';

$loader = new SplClassLoader('AQL', __DIR__ .'/lib');
$loader->register();

// use \AQL;

$aql = 	"
	market { name }
	ct_contract as contract { contract_name as test }
	a_third on ct_contract and market { third_field }
";
// try {
	$re = \AQL\Parser::parse($aql);	
// } catch (Exception $e) {
	print_r($aql . PHP_EOL);
	// print_r($e->getMessage() . PHP_EOL);
// }


print_r($re);
