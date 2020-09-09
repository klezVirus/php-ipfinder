
<?php
/**
* Classe di utility utile per parsare argomenti a linea di comando
* qualora se ne presentasse la necessita [molti comandi, opzioni e altre motivazioni]
* [LA CLASSE NON E' ATTUALMENTE UTILIZZATA]
*/
class inputReader{
	
	//Array di tutti i possibili comandi
	global $possible_commands = array(0 => "file", 1 => "ip"); 
	global $command_info = array("Il file di IP da analizzare");
								
	global $commands = array();
	
	//Funzione per parsare i comandi passati
	function parseGet($input){
		if($input != null){
			foreach($argv as $arg){
				$e=explode("=",$arg);
				if(count($e)==2){
					$_GET[$e[0]]=$e[1];
				}else{
					$_GET[$e[0]]=0;
				}
			}
		}
	}
	
	//Funzione che divide i comandi non posizionalmente
	function parseOrderedInput($input){
		if($input != null){
			if(!(array_search("-help", $input) and array_search("-h", $input) and array_search("--help", $input)){
				$target_file = $argv[1];
			} else{
				echo "Input non valido, digitare php nomefile.php --help, -help, o -h"
				return;
			}
		}
	}
	
	//Funzione che divide i comandi posizionalmente
	function parseNotOrderedInput($input){
		if($input != null){
			if(count($argv)>2){
			foreach($argv as $arg){
				$commands=explode("--",$arg);
				if(count($commands)==2){
					foreach($commands as $command){
						$_command = explode("=", $command);
						$command_name = $_command[0];
						$command_value = $_command[1];
						if($array_search($command_name, $possible_commands)){
							if(!isset(commands[$command_name]){
								commands[$command_name] = $command_value;
							}
						}
					}
				}
				}else{
					echo "Input non valido, digitare php nomefile.php --help, -help, o -h";
				}
			}
		}
	}
	
	//Getters
	
	function getCommands($input){
		if(isset($commands) and (count($commands)>0)){
			return $commands;
		}else{
			parseNotOrderedinput($input);
			return $commands;
		}
	}
	
	function getHelp(){
		echo 'Sintassi del comando: ' "\n" . 'php -f nomefile.php --command_name1=command_value1 ... --command_namen=command_valuen' . "\n"; 
		for($i = 0; $i<count($possible_commands); $i++){
			echo $$possible_commands[$i] . ' \t' . $command_info[$i];
		}
	}
	
	function getPossibleCommands(){
		return $this->$possible_commands;
	}
}


?>