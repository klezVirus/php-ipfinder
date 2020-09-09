<?php
/**########################################################################
*+------------------------------------------------------------------------+
*| Questa classe fornisce un implementazione che estende l'uso della ben
*| nota funzione curl di PHP, incapsulandone le funzionalit� in un oggetto
*| definito cURL.
*+------------------------------------------------------------------------+
*/
class cURL {
var $headers;
var $user_agent;
var $compression;
var $cookie_file;
var $proxy;
var $proxyport;

const URL_ALL = 'https://www.dan.me.uk/torlist/';
const URL_EXIT = 'https://www.dan.me.uk/torlist/?exit';

//Costruttore di classe
//Settato ad hoc per CARING
function cURL($cookies=TRUE, $cookie = 'PHPSESSID' ,$compression='gzip',$proxy=FALSE) {
	$this->headers[] = 'Accept: text/html, application/xhtml+xml, application/xml;q=0.9,*/*;q=0.8';
	$this->headers[] = 'Connection: Keep-Alive';
	$this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
	$this->user_agent = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:37.0) Gecko/20100101 Firefox/37.0';
	$this->compression=$compression;
	if($proxy==TRUE) { 
		$this->proxy = 'proxolo.cs.poste.it';
		$this->proxyport = '8080';
		}
	$this->cookies=$cookies;

	if ($this->cookies == TRUE) $this->cookie($cookie);
}
//Funzione di settaggio del cookie ( SETTATO PER CARING )
function cookie($cookie_file) {
	if (file_exists($cookie_file)) {
		$this->cookie_file=$cookie_file;
	} else {
		$this->cookie_file = fopen($cookie_file,'w') or $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions');
		fwrite($this->cookie_file, "j6ngn1v7p8thoq1m6ohd04rpi3");
		$this->cookie_file=$cookie_file;
		fclose($this->cookie_file);
	}
}
// Implementazione di una GET request per fetchare la lista dei server tor aggiornata
function fetch_tor_server_list() {
	$process = curl_init(self::URL_ALL);
	curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
	curl_setopt($process, CURLOPT_HTTPGET, true);
	//curl_setopt($process, CURLOPT_POSTFIELDS, $curl_post_data);
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($process, CURLOPT_HEADER, 0);
	curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
	if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
	if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
	curl_setopt($process,CURLOPT_ENCODING , $this->compression);
	curl_setopt($process, CURLOPT_TIMEOUT, 180);
	if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
	if ($this->proxyport) curl_setopt($process, CURLOPT_PROXYPORT, $this->proxyport);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
	$return = curl_exec($process);
	curl_close($process);
	return $return;
}

// Implementazione di una GET request per fetchare la lista degli exit point aggiornata
function fetch_tor_exitpoint_list() {
	$process = curl_init(self::URL_EXIT);
	curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
	curl_setopt($process, CURLOPT_HTTPGET, true);
	//curl_setopt($process, CURLOPT_POSTFIELDS, $curl_post_data);
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($process, CURLOPT_HEADER, 0);
	curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
	if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
	if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
	curl_setopt($process,CURLOPT_ENCODING , $this->compression);
	curl_setopt($process, CURLOPT_TIMEOUT, 180);
	if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
	if ($this->proxyport) curl_setopt($process, CURLOPT_PROXYPORT, $this->proxyport);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
	$return = curl_exec($process);
	curl_close($process);
	return $return;
}

// Implementazione di una GET request
function get($url) {
	$process = curl_init($url);
	curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
	curl_setopt($process, CURLOPT_HTTPGET, true);
	//curl_setopt($process, CURLOPT_POSTFIELDS, $curl_post_data);
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($process, CURLOPT_HEADER, 0);
	curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
	if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
	if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
	curl_setopt($process,CURLOPT_ENCODING , $this->compression);
	curl_setopt($process, CURLOPT_TIMEOUT, 180);
	if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
	if ($this->proxyport) curl_setopt($process, CURLOPT_PROXYPORT, $this->proxyport);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
	$return = curl_exec($process);
	curl_close($process);
	return $return;
}

//Funzione TEST connection
function test($url){
	// Non ancora implementata
}
//Implementazione di un ERROR return
function error($error) {
echo "<div style=\"text-align: center;\"><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana,serif; font-size: 10px'><b>cURL Error</b><br>$error</div></div>";
die;
}
}
?> 