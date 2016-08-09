<?php
class CRPconvert {
	public $UF2CEP = [
	  'AC'=>'69', 'AL'=>'57', 'AM'=>'69', 'AM'=>'69', 'AP'=>'68', 'BA'=>'4',  'CE'=>'6', 
	  'DF'=>'7',  'DF'=>'7',  'ES'=>'29', 'GO'=>'7',  'GO'=>'7',  'MA'=>'65', 'MG'=>'3', 
	  'MS'=>'79', 'MT'=>'78', 'PA'=>'6',  'PB'=>'58', 'PE'=>'5',  'PI'=>'64', 'PR'=>'8', 
	  'RJ'=>'2',  'RN'=>'59', 'RO'=>'76', 'RR'=>'69', 'RS'=>'9',  'SC'=>'8', 'SE'=>'49', 
	  'SP'=>'1',  'TO'=>'77', 'ZM'=>'0'
	];
	public $cep2prefix=NULL;
	private $tocep_regex=NULL;
	private $tocrp_regex=NULL;
	private $crp;
	private $keys;

	function __construct() {
		$this->keys = array_keys($UF2CEP);
		$this->tocep_regex = '/^'.join('|',$this->keys).'/gi';
	}

  /**
   * Set $crp accumulator.
   */
	function set($crp) {
		$crp = str_replace('-', '', strtoupper(trim($crp)) );
		if (preg_match('/^([A-Z][A-T]\d{4,5})\-?(\d{3,3})$/',$crp,$m) {
			$this->crp = "$m[1]-$m[2]"; 
			return true;
		} else
			return false;
	}

  /**
   * Set $crp accumulator by CEP string.
   */
	function setCEP($cep) {
		if ($cep2prefix===NULL) {
			$vals = array_values($this->UF2CEP)
			$this->cep2prefix = array_combine($vals,$this->keys);
			$this->tocrp_regex = '^/\s*('. join('|',$vals) .')(\d+)\-?(\d{3,3})\s*$/';
		}
		if ( preg_match($this->tocrp_regex,$cep,$m) )
			if (isset($this->cep2prefix[$m[1]])) 
			  return $this->get($this->cep2prefix[$m[1]].$m[2].$m[3]);
		return NULL;
	}

  /**
   * Show $crp accumulator as a CEP string.
   */
	function asCEP($crp=NULL,$retNull=false) {
		return preg_replace_callback(
			$this->tocep_regex,
			function ($m) use ($retNull,&$UF2CEP) {
				$prefix = strtoupper($m[0]);
				return isset($UF2CEP[$prefix])? $UF2CEP[$prefix]: ($retNull? NULL: $crp);
			},
			$crp? $crp: $this->crp;
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

