<?php
//
// at CRP's project root, `php demo/demo1.php | more`
// USAGE:
//   php demo/demo1.php
//   php demo/demo1.php 1 > part1_OfDemo1Htm
//   php demo/demo1.php 2 > part2_OfDemo1Htm
//

include('src/convert.php');


$f = 'data/CEP-to-CRP.csv';
$tab = NULL;
$modeJs = isset($argv[1])? $argv[1]: 0;

if (file_exists($f))
	$tab = array_map('str_getcsv', file($f));
else
	die("\nERRO $f não existe.\n");
$head = array_shift($tab);

$c = new CRPconvert();
foreach($tab as $r) {
	$a = array_combine($head,$r);
	$x = trim($a['CRP-from']);
	if ($modeJs && $x) {
			$msg = '';
			for($i=0;$i<3;$i++) {
				if ($i==0) {$input = $a['CRP-from']; $out = $a['CEP-from'];} //from
				elseif ($i==2) {$input = $a['CRP-to']; $out = $a['CEP-to'];}  // to
				else { 				// middle
					$c->set($a['CRP-from']); $from = $c->crp_int;
					$c->set($a['CRP-to']);
					$mid = round( ($c->crp_int+$from)/2 ) + rand(1, 999) - 499;
					echo "\n .. debug22: {$c->crp_int}+$from ".( round( ($c->crp_int+$from)/2 ));
					$c->setPart( $mid ); $input = $c->crp; $out = $c->asCEP();
				}
				$sep = $i? "; &nbsp;&nbsp; ": '';
				if ($modeJs==1)   // CRP  to CEP
					$msg .= "$sep$input=<script>document.write( assertMsg(cc.asCEP('$input'),'$out') )</script>";
				else           		// CEP to CRP
					$msg .= "$sep$out=<script>document.write( assertMsg(cc.set('$out').crp,'$input') )</script>";
			} // for
			echo "\n<li>$msg</li>";
	} elseif (!$modeJs && $x) { // CEP to
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


/// LIB:


function assertRocks($x,$msg=''){
	if ($x) echo "\n-- ASSERT: $msg.";
	else echo "\n-- assert ERROR at '$msg'.";
}
