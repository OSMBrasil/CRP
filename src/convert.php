<?php
/**
 * CRP conversions tool kit, PHP class library. See https://github.com/ppKrauss/CRP
 */

class CRPconvert {
	public $UF2CEP = [
	  'AC'=>'69', 'AL'=>'57', 'AM'=>'69', 'AM'=>'69', 'AP'=>'68', 'BA'=>'4',  'CE'=>'6',
	  'DF'=>'7',  'DF'=>'7',  'ES'=>'29', 'GO'=>'7',  'GO'=>'7',  'MA'=>'65', 'MG'=>'3',
	  'MS'=>'79', 'MT'=>'78', 'PA'=>'6',  'PB'=>'58', 'PE'=>'5',  'PI'=>'64', 'PR'=>'8',
	  'RJ'=>'2',  'RN'=>'59', 'RO'=>'76', 'RR'=>'69', 'RS'=>'9',  'SC'=>'8', 'SE'=>'49',
	  'SP'=>'1',  'TO'=>'77', 'ZM'=>'0'
	];
	public $cep2prefix=NULL; // UF2CEP's inverse associativity
	public $crp;        	// main register. Format LLDDDD-DDD for internal representation.
	public $crp_prefix; 	// cache for CRP's prefix
	public $crp_uf;    		// cache for CRP's UF
	public $crp_int;   		// cache for CRP's suffix in integer format

	public $debug=true; // set to false for stop debuging!

	private $tocep_regex=NULL;
	private $tocrp_regex=NULL;
	private $keys=NULL;

	function __construct($x=NULL) {
		$this->keys = array_keys($this->UF2CEP);
		$this->tocep_regex = '/^'.join('|',$this->keys).'/i';
		if ($x) $this->setAny($x);
	}

  /**
   * Set $crp accumulator.
   */
	function set($crp,$elseReset=true) {
		if ($crp && preg_match('/^\s*([A-Z][A-T])(\d{3,4})\-?(\d{3,3})\s*$/i',$crp,$m)) {
			//$aux = str_pad($m[2], 3, '0', STR_PAD_LEFT);
			$this->crp_prefix = $this->crp_uf = strtoupper($m[1]);
			if ($this->crp_prefix=='ZM') $this->crp_uf='SP';
			$this->crp = $this->crp_prefix."$m[2]-$m[3]";
			$this->crp_int = (int) "$m[2]$m[3]";
			return true;
		}
		if ($elseReset) $this->reset();
		if ($this->debug) die("\nERRO em set($crp)\n");
		else return false;
	}

	/**
   * Reset $crp accumulator and related caches.
   */
	function reset() {
		$this->crp = $this->crp_int = $this->crp_prefix = $this->crp_uf = NULL;
		return true;
	}

	/**
	 * Set $crp accumulator by CEP string.
	 */
	function setCEP($cep,$elseReset=true) {
		if ($this->cep2prefix===NULL) {
			$vals = array_values($this->UF2CEP);
			$this->cep2prefix = array_combine($vals,$this->keys);
			$this->tocrp_regex = '/^\s*('. join('|',$vals) .')(\d+)\-?(\d{3,3})\s*$/';
		}
		if ( preg_match($this->tocrp_regex,$cep,$m) )
			if (isset($this->cep2prefix[$m[1]]))
			  return $this->set($this->cep2prefix[$m[1]].$m[2].$m[3] , $elseReset);
		if ($elseReset) $this->reset();
		if ($this->debug) die("\nERRO em setCEP($cep)\n");
		return NULL;
	}

	/**
	 * Set $crp accumulator by any string (CEP or CRP).
	 */
	function setAny($x) {
		if (ctype_digit(substr(trim($x),0,1)))
			return $this->setCEP($x);
		else
			return $this->set($x);
	}

	/**
	 * Show $crp accumulator as a CEP string.
	 */
	function asCEP($crp=NULL,$retNull=false) {
		$U2C = &$this->UF2CEP;
		return preg_replace_callback(
			$this->tocep_regex,
			function ($m) use ($retNull,$U2C) {
				$prefix = strtoupper($m[0]);
				return isset($U2C[$prefix])? $U2C[$prefix]: ($retNull? NULL: $crp);
			},
			$crp? $crp: $this->crp
		);
	}

  /**
   * Show as standard CRP.
   */
	function asStd($crp=NULL,$retNull=false) {
    if ($crp!==NULL) $this->set($crp);
    if (preg_match('/^(.+?)(\d{3,3})$/',$this->crp,$m))
      return $m[1].'-'.$m[2];
    else
      return '';
  }

  /**
   * Show as compact CRP syntax (without "-").
   */
  function asCompact($crp=NULL) {
      if ($crp!==NULL) $this->set($crp);
      return $this->crp;
  }

} // class

// // // // // // // //
/// aux lib for demo.

function assertRocks($x,$msg=''){
	if ($x) echo "\n-- ASSERT: $msg.";
	else echo "\n-- assert ERROR at '$mgs'.";
}
