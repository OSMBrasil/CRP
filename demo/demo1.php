<?php
//
// at CRP's project root, `php demo/demo1.php | more`
//

include('src/convert.php');

function assertRocks($x,$msg=''){
	if ($x) echo "\n-- ASSERT: $msg.";
	else echo "\n-- assert ERROR at '$msg'.";
}


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
	$x = trim($a['CRP-from']);
	if ($x) {
		$c->set($x);
		$cep = $c->asCEP();
		assertRocks($cep==$a['CEP-from'],  "i=$x: CRP({$a['CEP-from']})={$c->crp} =ctx+{$c->crp_int} (CEP1 $cep)");

		$x =$a['CEP-to'];
		$c->set($x);
		$cep = $c->asCEP();
		assertRocks($c->asCEP()==$a['CEP-to'], "i=$x: CRP({$a['CEP-to']})={$c->crp} =ctx+{$c->crp_int}  (CEP2 $cep)");

		$c->set($a['CEP-from']);
		assertRocks($c->asCEP()==$a['CEP-from'],"any({$a['CEP-from']})={$c->crp} =ctx+{$c->crp_int}");
		print "\n";
	} // if
} // for
