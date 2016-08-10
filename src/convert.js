/**
 * CRP conversions tool kit, Javascript class library. See https://github.com/ppKrauss/CRP
 */


var CRPconvert = function () {
	this.UF2CEP = {
		"AC":"69", "AL":"57", "AM":"69", "AP":"68", "BA":"4",  "CE":"6",  "DF":"7",
		"ES":"29", "GO":"7",  "MA":"65", "MG":"3",  "MS":"79", "MT":"78", "PA":"6",
		"PB":"58", "PE":"5",  "PI":"64", "PR":"8",  "RJ":"2",  "RN":"59", "RO":"76",
		"RR":"69", "RS":"9",  "SC":"8",  "SE":"49", "SP":"1",  "TO":"77", "ZM":"0"
	};
	this.cep2prefix={}; // UF2CEP's inverse associativity
	this.crp=null;        	// main register. Format LLDDDD-DDD for internal representation.
	this.crp_prefix=null; 	// cache for CRP's prefix
	this.crp_uf=null;    		// cache for CRP's UF
	this.crp_int=null;   		// cache for CRP's suffix in integer format
	this.debug=true; // set to false for stop debuging!
	this.tocep_regex=null;  // private
	this.tocrp_regex=null;  // private

	this.keys=Object.keys(this.UF2CEP);  // private
	this.tocep_regex = new RegExp( '^'+this.keys.join('|') , 'i');
	var vals=[]; var v;
	for (var k in this.UF2CEP) {
		v = this.UF2CEP[k];
		this.cep2prefix[v] = k;
		vals.push(v);
	}
	this.tocrp_regex = new RegExp( '/^\\s*('+ vals.join('|') +')(\d+)\-?(\d{3,3})\\s*$/' );
	this.crp_regex = /^\s*([A-Z][A-T])(\d{3,4})\-?(\d{3,3})\s*$/i;
}



/**
 * Set $crp accumulator.
 */
CRPconvert.prototype.set = function (crp) {
	var m;
	if ((m = this.crp_regex.exec(crp)) !== null) {
		this.crp_prefix = this.crp_uf = m[1].toUpperCase();
		if (this.crp_uf=='ZM') this.crp_uf='SP';
		this.crp = this.crp_prefix + m[2] +'-'+ m[3];
		this.crp_int = parseInt(m[2]+m[3]);
		return true;
	} elseif (this.debug)
		alert("\nERRO em set($crp)\n");
	return false;
}


/////////////
///...
//  lixo fazendo
//...


/**
* Set $crp accumulator by CEP string.
*/
CRPconvert.prototype.setCEP = function (cep) {
	if (this.cep2prefix===null) {
		var vals = this.keys.map(function(v) { return this.UF2CEP[v]; });
		this.cep2prefix = array_combine($vals,this.keys);
		this.tocrp_regex = '/^\s*('. join('|',$vals) .')(\d+)\-?(\d{3,3})\s*$/';
	}
	if ( preg_match(this.tocrp_regex,$cep,$m) )
		if (isset(this.cep2prefix[$m[1]]))
			return this.set(this.cep2prefix[$m[1]].$m[2].$m[3]);
	if (this.debug) die("\nERRO em setCEP()\n");
	return null;
}

/**
* Show $crp accumulator as a CEP string.
*/
CRPconvert.prototype.asCEP = function (crp=null,retNull=false) {
	$U2C = &this.UF2CEP;
	return preg_replace_callback(
		this.tocep_regex,
		function ($m) use ($retNull,$U2C) {
			$prefix = strtoupper($m[0]);
			return isset($U2C[$prefix])? $U2C[$prefix]: ($retNull? null: $crp);
		},
		$crp? $crp: this.crp
	);
}

/**
* Show as standard CRP.
*/
CRPconvert.prototype.asStd = function (crp=null,retNull=false) {
	if ($crp!==null) this.set($crp);
	if (preg_match('/^(.+?)(\d{3,3})$/',this.crp,$m))
		return $m[1].'-'.$m[2];
	else
		return '';
}

/**
* Show as compact CRP syntax (without "-").
*/
CRPconvert.prototype.asCompact = function (crp=null) {
	if ($crp!==null) this.set($crp);
	return this.crp;
}
