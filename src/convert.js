/**
 * CRP conversions tool kit, Javascript class library. See https://github.com/ppKrauss/CRP
 * Please review for annotations, https://developers.google.com/closure/compiler/docs/js-for-compiler?csw=1
 */

/**
* Returns a flip object.
* @param {Array} trans.
* @return {Array} tmp_ar.
*/
function object_flip( trans ) {
  // ... or use underscorejs.org functions like invert and union
    var key, tmp_ar = {};
    for (key in trans) //pure JSON object not need, if (trans.hasOwnProperty(key))
      tmp_ar[trans[key]] = key;
    return tmp_ar;
} // not exist flip... Workarounds: http://stackoverflow.com/a/14810722


/**
 * CRP conversions tool kit class.
 */
var CRPconvert = function (x=null) {

  this.prefMain_rgx = /^(699|693|689|69|65|64|79|78|77|76|59|58|57|5|49|4|29|2|9|3|1|0)/;

  this.prefExtra_rgx= /^(?:(6[0-3])|(6(?:[67][0-9]|8[0-8]))|(7(?:3[0-6]|[01]|2[0-7]))|(7(?:2[8-9]|3[7-9]|[45]|6[0-7]))|(8[0-7])|(8[8-9]))/;

	this.UF2prefExtra = {"CE":"6", "PA":"6", "DF":"7", "GO":"7", "PR":"8", "SC":"8"};
	this.prefMain2uf = {
    "699":"AC","693":"RR","689":"AP","69":"AM","65":"MA","64":"PI","79":"MS","78":"MT",
    "77":"TO","76":"RO","59":"RN","58":"PB","57":"AL","5":"PE","49":"SE","4":"BA","29":"ES",
    "2":"RJ","9":"RS","3":"MG","1":"SP","0":"ZM"
  };

  this.debug = true;

  this.UF2prefFull	  = Object.assign( object_flip(this.prefMain2uf), this.UF2prefExtra );
  //this.prefExtra2UF   = Object.keys(this.UF2prefExtra);
  this.prefExtra2UF   = new Array();
  this.prefExtra2pref = new Array();
  for (var k in this.UF2prefExtra) {
      this.prefExtra2UF.push(k);  // keys
      this.prefExtra2pref.push(this.UF2prefExtra[k]); //values
  }
  if (x) this.set(x);
}; // class CRPconvert constructor


/**
 * Set context by CEP or CRP or prefix.
 * @return 2 when CEP, 1 when CRP, null for error.
 */
CRPconvert.prototype.setContext = function (x) {
  x = String(x).trim();
  if ( x.charCodeAt(0) > 64 )  // check it is not a digit
    return this.setContextByCRP(x);
  else
    return 2*this.setContextByCEP(x);
}


/**
 * Set CRP register by "contexted CRP" (CRP without prefix).
 * @return 1 when sucess, null for error.
 */
CRPconvert.prototype.setPart = function (x) {
  var m = /^(\d{1,4})-?(\d{3,3})$/.exec(x);
  if ( m==null )
		return this.error(1,"valor '"+x+"' incompleto ou inválido como uncontexted CRP.");
	this.crp_int = parseInt(m[1]+m[2]);
	this.crp     = this.crp_uf + m[1] +'-'+ m[2];
	return this;
}

CRPconvert.prototype.state = function () {
  return {
    crp:this.crp,
    crp_uf:this.crp_uf, crp_int:this.crp_int, crp_uf_isReal:this.crp_uf_isReal,
    crp_pref:this.crp_pref
  };
}

/**
 * Set CRP register by full CEP or CRP.
 * @return 1 when sucess, null for error.
 */
CRPconvert.prototype.set = function (x) {
	var ctx = this.setContext(x); // 2 is CEP
	if (!ctx) return null;
	return this.setPart(
    x.substr( (ctx==2)? this.crp_pref.length: 2 )  // removing CEP or CRP prefix
  );
}

/**
 * Reset $crp accumulator and related caches.
 */
CRPconvert.prototype.reset = function () {
 	this.crp = this.crp_int = this.crp_uf = this.crp_uf_isReal = this.crp_pref = null;
  return this;
}

/**
 * Show $crp accumulator as a CEP string.
 */
CRPconvert.prototype.asCEP = function (x=null,compact=false) {
  if (x) {if (this.set(x)==null) return null;}
  return this.crp? this.compact(
    String(this.crp_pref) + this.crp.substr(2),
    compact
  ):  null;
}


//// PRIVATE METHODS:

/**
 * get context of a CEP or CEP prefix.
 */
CRPconvert.prototype.setContextByCEP = function (cep) {
		if (!cep)
		 	return this.error(2,"CEP vazio");
		this.reset();
    var m = this.prefExtra_rgx.exec(cep);
		if ( m!= null ) {
      var j=1;  // infelizmente m.length não se limita ao ultimo nao-null, requer scan
      for(; j<=m.length  && m[j]==undefined; j++);
			this.setUF( this.prefExtra2UF[j-1] );
			this.crp_pref = this.prefExtra2pref[j-1]; // ou UF2pref, economiza uma array.
		} else if ( (m = this.prefMain_rgx.exec(cep))!= null ) {
			this.crp_pref = m[0];
			this.setUF( this.prefMain2uf[this.crp_pref] );
		} else
			return this.error(3,"CEP '"+cep+"' em intervalo inválido");
		return 1;
	}

/**
 * Get context of a CRP or CRP prefix.
 * @param {crp} String the input
 */
 CRPconvert.prototype.setContextByCRP = function (crp) {
		if (crp.length<2)
		 	return this.error(4,"CRP vazio");
		var aux = crp.substr(0,2).toUpperCase();
		if ( this.UF2prefFull.hasOwnProperty(aux) ) {
			this.reset();
			this.setUF(aux);
			this.crp_pref = this.UF2prefFull[aux];
			return 1;
		} else
			return this.error(5,"CRP '"+crp+"' com prefixo '"+aux+"' desconhecido");
	}

/** Atribui UF e flag indicando se é código de UF oficial */
CRPconvert.prototype.setUF = function (uf) {
  this.crp_uf = uf;
  this.crp_uf_isReal = (uf!='ZM'); // true when crp_uf is real.
}

/**
 * Compact code (removes '-').
 */
CRPconvert.prototype.compact = function (x,flag=false) {
		return flag? x.replace('-',''): x;
}

CRPconvert.prototype.error = function (cod,msg='') {
	if (msg) msg = ": "+msg;
	if (this.debug)
		alert("\nERROR-"+cod+msg+"\n");
	if (this.onerror_reset) this.reset();
	return null;
}
