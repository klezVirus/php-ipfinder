<?php 
require_once 'lib/cURL.php';
/***************************************************************************************
Semplice script per verificare se un IP è un exit point TOR

****************************************************************************************/

function loadTorServerList($path_to_list){
	$fp = fopen($path_to_list , 'w');
	$curl = new cURL();
	$data = $curl->fetch_tor_server_list();
	if(!$data){
		fclose($fp);
		throw new Exception("Cannot fetch tor list from server");
	}
	fwrite($fp, $data);
	fclose($fp);
}

function loadTorExitList($path_to_list){
	$fp = fopen($path_to_list , 'w');
	$curl = new cURL();
	$data = $curl->fetch_tor_exitpoint_list();
	if(!$data){
		fclose($fp);
		throw new Exception("Cannot fetch tor list from server");
	}
	fwrite($fp, $data);
	fclose($fp);
}

function IsTorServer($ip, $path_to_list){
	if(!file_exists($path_to_list)){
		try {
			loadTorServerList($path_to_list);
		}
		catch (Exception $e){
			die($e->getMessage());
		}
	}
	$fp = fopen($path_to_list , 'r');
	while(!feof($fp)){
		$torIp = fgets($fp);
		if ($ip == $torIp) {
			return "YES";
			} 		
		}
	return "NO"; 
}

function IsTorExitPoint($ip, $path_to_list){
	if(!file_exists($path_to_list)){
		try {
			loadTorExitList($path_to_list);
		}
		catch (Exception $e){
			die($e->getMessage());
		}
	}
	$fp = fopen($path_to_list , 'r');
	while(!feof($fp)){
		$torIp = fgets($fp);
		if ($ip == $torIp) {
			return "YES";
			} 		
		}
	return "NO"; 
}

function CheckTorExitPoint($ip){
	if (gethostbyname(ReverseIPOctets($ip).".ip-port.exitlist.torproject.org")=="127.0.0.2") {
		return "YES";
	} else {
		return "NO";
	} 
}
function ReverseIPOctets($inputip){
	$ipoc = explode(".",$inputip);
	return $ipoc[3].".".$ipoc[2].".".$ipoc[1].".".$ipoc[0];
}
?> 

