<?php

require_once(__DIR__ . '/vendor/autoload.php');

use phpWhois\Whois;

class Whois_obj {
	protected $whois;
	
	function __construct(){
		$this->whois = new Whois();
		$this->whois->deep_whois=TRUE;
	}
	
	function whoisCall($ip){
		return $this->whois->Lookup($ip, false);
}

}
?>