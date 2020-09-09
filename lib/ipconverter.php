<?php
/*
* ###############################################################
* #  Util Class : Ip Converter
* #  This class is meant to be a wrapper for the functions
* #  which convert an IP to an Int and vice-versa.
* #  This kind of conversion can be useful for operations
* #  such as database storing, indicization, ordering and 
* #  accessing.
* ###############################################################
*/
//IP to INT conversion
function ip2int($ip){
	$octets = explode(".", $ip);
	print_r($octets);
	$int = $octets[0] * 16777216
        + $octets[1] * 65536
        + $octets[2] * 256
        + $octets[3]
        ;
	return $int;
}
//INT to IP conversion
function int2ip($int){
    $DIV = 256;
	$octets = array();
	$octets[0] = $int%$DIV;
	$tmp = floor($int/$DIV);
	$octets[1] = $tmp%$DIV;
	$tmp = floor($tmp/$DIV);
	$octets[2] = $tmp%$DIV;
	$tmp = floor($tmp/$DIV);
	$octets[3] = $tmp%$DIV;
	$ip = $octets[3] . "." . $octets[2] . "." . $octets[1] . "." . $octets[0] ;
	return $ip;
}
// Validate integer value
function isInt($input){
	if(preg_match('@^[\d]*$@', $input)){
		return true;
	}
	return false;
}
//Validate IP value
function isIp($input){
	if(preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/' ,$input)){
		return true;
	}
	return false;
}


?>