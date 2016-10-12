<?php
/**
 * CRP base regular expressoins (rgx) generation. See https://github.com/ppKrauss/CRP
 * php src/rgxGen.php > script_draft.txt
 * Edit data source at https://docs.google.com/spreadsheets/d/1Nkw0RXSiZvDxHFCo8YzXmfoFnnl4swFcADBx884Pmrs/
 *  melhor resolver nos dados !
 */

$f = 'data/CEP-to-CRP.csv';
$t = array_map('str_getcsv', file($f));
$thead = array_shift($t);

$CEPranges = [];
$pref2uf = [];
$uf2pref = [];
foreach($t as $r) {
	$a = array_combine($thead,$r);
	if ($a['range-prefix']>'') { // ignore lines with "other part"
		if (!isset($CEPranges[$a['range-prefix']]))
			$CEPranges[$a['range-prefix']]=[];
		//if (isset($CEPranges[$a['range-prefix']][$a['CRP-prefix']]))
		$CEPranges[$a['range-prefix']][$a['CRP-prefix']] = $a['extra-prefix'];
	}
}

$prefMain2uf   =[];
$prefMain_rgx = '';

$prefExtra2UF=[];
$prefExtra_rgx = [];
$prefExtra2pref =[];

foreach ($CEPranges as $pref => $r) {
	if (count($r)==1) {
		$r = array_keys($r);
		$prefMain2uf[$pref]=$r[0]; //r0=uf
		$prefMain_rgx .= ($prefMain_rgx? '|': '').$pref;
	} else {
		$aux = array_keys($r);
		$prefExtra2UF = array_merge($prefExtra2UF,$aux);
		$prefExtra2pref  = array_merge($prefExtra2pref,array_fill(0,count($aux),$pref));
		// falta aqui converter cada key na ordem e depois sequencializar prefixos-siglas por item
		$k = [];
		foreach (array_values($r) as $rgx)
			$k[] = $pref.$rgx;
		$prefExtra_rgx[$pref] = '('. join(')|(',$k) .')';
	}
}

$xx = array_combine($prefExtra2UF,$prefExtra2pref); // new for changed scripts
$yy = array_flip($prefMain2uf);   // new for changed scripts


// RESULTS:
$prefMain_rgx  = "/^($prefMain_rgx)/";
$prefExtra_rgx = '/^'. join('|',$prefExtra_rgx) .'/';

$php_prefExtra2UF = php_encode($prefExtra2UF);
$js_prefExtra2UF  = json_encode($prefExtra2UF);
$php_prefExt    = php_encode($prefExtra2pref);
$js_prefExt     = json_encode($prefExtra2pref);
$php_prefMain2uf = php_encode($prefMain2uf);
$js_prefMain2uf  = json_encode($prefMain2uf);

$php_xx = php_encode($xx);
$js_xx = json_encode($xx);

$php_yy = php_encode($yy);
$js_yy = json_encode($yy);

$preLens = []; // new for SQL direc to_char(int,format) convertions of crc_int.
foreach(array_merge($xx,$yy) as $uf=>$pref) 
	$preLens[$uf] = 'fm'.str_pad('0-000', 9-strlen($pref), "0", STR_PAD_LEFT);
ksort($preLens);

$php_preLens = php_encode($preLens);
$js_preLens = json_encode($preLens);

print <<<scripts

JAVSCRIPT:
	this.prefMain_rgx = $prefMain_rgx;
	this.prefExtra_rgx= $prefExtra_rgx;
	this.prefExtra2UF = $js_prefExtra2UF;
	this.UF2prefExtra = $js_xx;
	this.prefExtra2pref = $js_prefExt;
	this.prefMain2uf = $js_prefMain2uf;
	this.uf2prefMain = $js_yy;
	this.preLens     = $js_preLens;
PHP:
	\$this->prefMain_rgx = '$prefMain_rgx';
	\$this->prefExtra_rgx= '$prefExtra_rgx';
	\$this->prefExtra2UF = $php_prefExtra2UF;
	\$this->UF2prefExtra = $php_xx;
	\$this->prefExtra2pref = $php_prefExt;
	\$this->prefMain2uf = $php_prefMain2uf;
	\$this->uf2prefMain = $php_yy;

scripts;

/////
function php_encode($a) {
	return preg_replace( '/\s+/s', '', var_export($a,true) );
}
