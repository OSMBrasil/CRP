<?php
//
// at CRP's project root, `php demo/demo1.php | more`
//

include('src/convert.php');

$f = 'data/CEP-to-CRP.csv';
$tab = NULL;

if (file_exists($f))
	$tab = array_map('str_getcsv', file($f));
else
	die("\nERRO $f não existe.\n");
$head = array_shift($tab);

$c = new CRPconvert();
foreach($tab as $r) {
	$a = array_combine($head,$r);
	$from = trim($a['CRP-from']);
	if ($from) {
		$c->setAny($from);
		assertRocks($c->asCEP()==$a['CEP-from'],"CRP(from={$a['CEP-from']})={$c->crp}");

		$c->setAny($a['CRP-to']);
		assertRocks($c->asCEP()==$a['CEP-to'],"CRP(to={$a['CEP-to']})={$c->crp}");

		$c->setAny($a['CEP-from']);
		assertRocks($c->asCEP()==$a['CEP-from'],"any(CEP {$a['CEP-from']})={$c->crp}");
	} // if
} // for
