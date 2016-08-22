<?php
/**
 * CRP conversions tool kit, PHP class library. See https://github.com/ppKrauss/CRP
 * USE: $c = new CRPconvert('MS123-456'); print $c->asCEP();
 * @see demo/demo1.php
 */

class CRPconvert {

	public $crp      =NULL; // full CRP register
	public $crp_uf   =NULL; // cache context CRP prefix
	public $crp_int  =NULL; // cache contextualized integer
	public $crp_pref =NULL; // cache context CEP prefix

	public $debug    =true; // set to false for stop debuging!

	/**
   * Set context by CEP or CRP or prefix.
	 * @return 2 when CEP, 1 when CRP, NULL for error.
   */
	private function setContext($x) {
		$x = trim($x);
		if (ctype_digit(substr($x,0,1)))
			return 2*$this->setContextByCEP($x);
		else
			return $this->setContextByCRP($x);
	}

	/**
   * Set CRP register by "contexted CRP" (CRP without prefix).
	 * @return 1 when sucess, NULL for error.
   */
	function setPart($x) {
		if (!preg_match('/^(\d{1,4})-?(\d{3,3})$/',$x,$m))
			return $this->error(1,"valor '$x' incompleto ou inválido como uncontexted CRP.");
		$this->crp_int = (int) "$m[1]$m[2]";
		$this->crp     = "{$this->crp_uf}$m[1]-$m[2]";
		return 1;
	}

	/**
   * Set CRP register by full CEP or CRP.
	 * @return 1 when sucess, NULL for error.
   */
	function set($x) {
		$ctx = $this->setContext($x); // 2 is CEP
		if (!$ctx) return NULL;
		$aux = substr( $x, ($ctx==2)? strlen($this->crp_pref): 2 ); // removing CEP or CRP prefix
		return $this->setPart($aux);
	}

	/**
   * Reset $crp accumulator and related caches.
   */
	function reset() {
		$this->crp = $this->crp_int = $this->crp_uf = $this->crp_pref = NULL;
	}

	/**
	 * Show $crp accumulator as a CEP string.
	 */
	function asCEP($crp=NULL,$compact=false) {
		if ($crp) $this->setContextByCRP($crp);
		return $this->compact(
			((string) $this->crp_pref) . substr($this->crp,2),
			$compact
		);
	}

  /**
   * Show as standard CRP.
   */
	function asCRP($cep=NULL,$compact=false) {
		if ($cep) $this->setContextByCEP($cep);
		return $this->compact($this->crp,$compact);
  }

	// // // // // //
	// INTERNAL USE

	var $prefMain_rgx  = '/^(?:699|693|689|69|65|64|79|78|77|76|59|58|57|5|49|4|29|2|9|3|1|0)/';
	var $prefExtra_rgx = '/^(?:(6[0-3])|(6(?:[67][0-9]|8[0-8]))|(7(?:3[0-6]|[0-2][0-7]))|(7(?:2[8-9]|3[7-9]|[45][0-9]|6[0-7]))|(8[0-7])|(8[8-9]))/';

	var $UF2prefExtra = ['CE'=>'6', 'PA'=>'6', 'DF'=>'7', 'GO'=>'7', 'PR'=>'8', 'SC'=>'8'];
	var $prefMain2uf  = array(
		'699'=>'AC','693'=>'RR','689'=>'AP','69'=>'AM','65'=>'MA','64'=>'PI','79'=>'MS',
		'78'=>'MT','77'=>'TO','76'=>'RO','59'=>'RN','58'=>'PB','57'=>'AL','5'=>'PE','49'=>'SE',
		'4'=>'BA','29'=>'ES','2'=>'RJ','9'=>'RS','3'=>'MG','1'=>'SP','0'=>'ZM'
	);

	public $err_cod ='';   // last error code
	public $err_msg ='';   // last error message
	public $onerror_reset =true; // set to false for preserve crp registers

	function __construct($x=NULL) {
		$this->UF2prefFull	  = array_merge(array_flip($this->prefMain2uf), $this->UF2prefExtra);
		$this->prefExtra2UF   = array_keys($this->UF2prefExtra);
		$this->prefExtra2pref = array_values($this->UF2prefExtra);
		if ($x) $this->set($x);
	}

	/**
   * get context of a CEP or CEP prefix.
   */
	private function setContextByCEP($cep) { //
		if (!$cep)
		 	return $this->error(2,"CEP vazio");
		$this->reset();
		if ( preg_match($this->prefExtra_rgx,$cep,$m) ) {
			$aux = count($m)-2;
			$this->crp_uf   = $this->prefExtra2UF[$aux];
			$this->crp_pref = $this->prefExtra2pref[$aux];
		} elseif ( preg_match($this->prefMain_rgx,$cep,$m) ) {
			$this->crp_pref = $m[0];
			$this->crp_uf   = $this->prefMain2uf[$this->crp_pref];
		} else
			return $this->error(3,"CEP '$cep' em intervalo inválido");
		return 1;
	}

	/**
   * get context of a CRP or CRP prefix.
   */
	private function setContextByCRP($crp) {
		if (strlen($crp)<2)
		 	return $this->error(4,"CRP vazio");
		$aux = strtoupper(substr($crp,0,2));
		if (isset($this->UF2prefFull[$aux])) {
			$this->reset();
			$this->crp_uf   = $aux;
			$this->crp_pref = $this->UF2prefFull[$aux];
			return 1;
		} else
			return $this->error(5,"CRP '$crp' com prefixo '$aux' desconhecido");
	}

	/**
   * Compact code (removes '-').
   */
	private function compact($x,$compact=false) {
		return $compact? str_replace('-','',$x): $x;
  }

	private function error($cod,$msg='') {
		if ($msg) $msg = ": $msg";
		if ($this->debug)
			die("\nERROR-$cod$msg\n");
		if ($this->onerror_reset) $this->reset();
		return NULL;
	}

} // class
