<?php
//Necessita delle seguenti API
require "dbip-client.class.php";

class dbipObject{

	private $api_key;
	private $dbip;
	
	public function dbipObject($api_key){
		$this->api_key = $api_key;
		try {
			$this->dbip = new DBIP_Client($this->api_key);
			/*
			echo "keyinfo:\n";
			foreach ($this->dbip->Get_Key_Info() as $k => $v) {
				echo "{$k}: {$v}\n";
			}*/
			return $this->dbip;
		}catch (Exception $e) {
			echo $e;

		}
	} 

	public function getClient(){
		return $this->dbip;
	}

	public function getApiKey(){
		return $this->api_key;
	}

	public function setApiKey($newKey){
		$this->api_key = $newKey;
	}
}
?>
