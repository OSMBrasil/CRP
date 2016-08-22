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
	if ($a['range-prefix']!='x') { // ignore lines with "other part"
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

// RESULTS:
$prefMain_rgx  = "/^($prefMain_rgx)/";
$prefExtra_rgx = '/^'. join('|',$prefExtra_rgx) .'/';

$php_prefExtra2UF = php_encode($prefExtra2UF);
$js_prefExtra2UF  = json_encode($prefExtra2UF);
$php_prefExt    = php_encode($prefExtra2pref);
$js_prefExt     = json_encode($prefExtra2pref);
$php_prefMain2uf = php_encode($prefMain2uf);
$js_prefMain2uf  = json_encode($prefMain2uf);

print <<<scripts

JAVSCRIPT:
	this.prefMain_rgx = $prefMain_rgx;
	this.prefExtra_rgx= $prefExtra_rgx;
	this.prefExtra2UF = $js_prefExtra2UF;
	this.prefExtra2pref = $js_prefExt;
	this.prefMain2uf = $js_prefMain2uf;

PHP:
	\$this->prefMain_rgx = '$prefMain_rgx';
	\$this->prefExtra_rgx= '$prefExtra_rgx';
	\$this->prefExtra2UF = $php_prefExtra2UF;
	\$this->prefExtra2pref = $php_prefExt;
	\$this->prefMain2uf = $php_prefMain2uf;

scripts;

/////
function php_encode($a) {
	return preg_replace( '/\s+/s', '', var_export($a,true) );
}
