<?php 
require_once 'lib/PHPExcel.php'; //standard con documentazione

//generic open file funtion
	function file_open($f_name){
	if(fopen($f_name, "r")==null){
		return true;
	}
	$fp = fopen($f_name, "r");
	$content = "";
	while(true) {
		$data = fread($fp, 8192);
		if(strlen($data)==0){
			break;
		}
		$content .= $data;
	}
	fclose($fp);
	return $content;
	}

	//generic write file function
	function file_write($filename,$content) {
        $fp = fopen($filename, 'w');
        if (fwrite($fp, $content)) {
                fclose($fp);
        return true;
        } else {
           return false;
        }
}
//generic write file function
	function file_append($filename,$content) {
        $fp = fopen($filename, 'a');
        if (fwrite($fp, $content . "\n")) {
                fclose($fp);
        return true;
        } else {
           return false;
        }
}
	function countRows($file){
		$count=0;
		$fp = @fopen($file, 'r');
		while(!feof($fp)){
			fgets($fp);
			$count++;
		}
		fclose($fp);
		return $count;
}
// Splitting functions 
function split_xls($xls, $limit = 10){
	//variabile utile all'indicizzazione su excel
	$letters = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	$objPHPExcel = PHPExcel_IOFactory::load($xls);
	// prende il primo foglio nel workbook 
	// Notare che i fogli sono indicizzati da 0 
	$objWorksheet = $objPHPExcel->getSheet(0);
	//Colonna obiettivo --> Chiama la funzione 
	$column_target = getTargetColumn($objPHPExcel, $letters);
	$highestRow = $objWorksheet->getHighestRow();
	$fileNumber = ceil(($highestRow)/$limit);
	if($highestRow > 2 * $limit){
		for($j=1; $j <= $fileNumber; $j++){
			for($i=($limit*($j-1))+1 ; $i<($limit*$j) && $i<$highestRow; $i++){
				//Ritiro l'IP all' i-esima riga
				$ip = $objPHPExcel->getActiveSheet()->getCell($column_target.$i)->getValue();
				//Eseguiamo un trim per rimuovere eventuali trailing space
				trim($ip);
				file_append("input_files/$j.txt", $ip);
			}
		}
	}else{
		for($i=2;$i<$highestRow;$i++){
			//Ritiro l'IP all' i-esima riga
				$ip = $objPHPExcel->getActiveSheet()->getCell($column_target.$i)->getValue();
				//Eseguiamo un trim per rimuovere eventuali trailing space
				trim($ip);
				file_append("input_files/A.txt", $ip);
			}
		}
	}
function split_csv($csv, $limit = 10){

	$fp = @fopen($csv, 'r');
	$highestRow = countRows($csv);
	$fileNumber = ceil(($highestRow)/$limit);
	$answer = getTargetField($csv);
	if($answer==NULL){
		echo "Impossibile effettuare lo splitting: Field corretto non trovato.";
		return;
	}
	if($highestRow > 2 * $limit){
		for($j=1; $j <= $fileNumber; $j++){
			for($i=($limit*($j-1))+1 ; $i<($limit*$j) && $i<$highestRow; $i++){
				$line = fgetcsv($fp, 0, $answer[0]);
				//Ritiro l'IP all' i-esima riga
				$ip = $line[$answer[1]];
				//Eseguiamo un trim per rimuovere eventuali trailing space
				trim($ip);
				file_append("input_files/$j.txt", $ip);
			}
		}
	}else{
		for($i=2;$i<$highestRow;$i++){
			$line = fgetcsv($fp, 0, $answer[0]);
			//Ritiro l'IP all' i-esima riga
			$ip = $line[$answer[1]];
			//Eseguiamo un trim per rimuovere eventuali trailing space
			trim($ip);
			file_append("input_files/A.txt", $ip);
		}
	}
	fclose($fp);
}
function split_txt($txt, $limit = 10){
	
	$highestRow = countRows($txt);
	$fileNumber = ceil(($highestRow + 1)/$limit);
	$fp = @fopen($txt,'r');
	if($highestRow > 2 * $limit){
		for($j=1; $j <= $fileNumber; $j++){
			for($i=($limit*($j-1))+1 ; $i<($limit*$j) && $i<$highestRow; $i++){
				//Ritiro l'IP all' i-esima riga
				$ip = fgets($fp);
				//Eseguiamo un trim per rimuovere eventuali trailing space
				trim($ip);
				file_append("input_files/$j.txt", $ip);
			}
		}
	}else{
		for($i=2;$i<$highestRow;$i++){
			//Ritiro l'IP all' i-esima riga
			$ip = fgets($fp);
			//Eseguiamo un trim per rimuovere eventuali trailing space
			trim($ip);
			file_append("input_files/A.txt", $ip);
			}
		}

}

//Funzione che trova la colonna di indirizzi IP nell'excel e ne restituisce l'indice
function getTargetColumn($objPHPExcel , $letters){
	$row =1; //Riga iniziale
	$column = 0; // Indice colonna iniziale
	$column_target = null; // Indice colonna obiettivo
	//Finch� sulla prima riga c'� un valore cerco la colonna relativa agli IP
	while($objPHPExcel->getActiveSheet()->getCell($letters[$column].$row)->getValue()!=null){
		// Fetch del valore della cella
		$value = $objPHPExcel->getActiveSheet()->getCell($letters[$column].$row)->getValue();
		//Se ho trovato la colonna relativa agli IP
		if(preg_match('@^(src|list|dst|source|ip)([\w|\W]*)(ip|ips|address|addr)$@i', $value) || preg_match('@^\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b$@', $value)){
			// Ne salvo l'indice
			$column_target = $letters[$column];
			// E lo restituisco
			return $column_target;
		}else{
			// Altrimenti passo alla colonna successiva
			$column++;
		}
	}
}
//Funzione che trova la colonna di indirizzi IP nell'excel e ne restituisce l'indice
function getTargetField($csvName){
	$fp = @fopen($csvName, 'r');
	$array = fgetcsv($fp, 0, ',');
	fclose($fp);
	$fields = count($array);
	if($fields > 1){
		for($column = 0; $column < $fields; $column++){
			$line = trim($array[$column], " _^-'\"\t\n\r\0\x0B");
			if(preg_match('@^(src|list|dst|source|ip)([\w|\W]*)(ip|ips|address|addr)$@i', $line) || preg_match('@^\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b$@', $line)){
				return array( 0 => "," , 1 => $column);
			}
		}
	}
	else {
		$line = trim($array[0], " _^-'\"\t\n\r\0\x0B");
		if(preg_match('@^(src|list|dst|source|ip)([\w|\W]*)(ip|ips|address|addr)$@i', $line) || preg_match('@^\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b$@', $line)){
				return array( 0 => "," , 1 => 0);
		}else if(preg_match('@([\w|\W]*)src([\w|\W]*)ip([\w|\W]*)@i', $line) || preg_match('@([\w|\W]*)([\d]{1,3}).([\d]{1,3}).([\d]{1,3}).([\d]{1,3})([\w|\W]*)@i', $line)){
			$fp = @fopen($csvName, 'r');
			unset($array);
			$array = fgetcsv($fp ,0, ";");
			fclose($fp);
			$fields = count($array);
			if($fields > 1){
				for($column = 0; $column < $fields; $column++){
					$line = trim($array[$column], " _^-'\"\t\n\r\0\x0B");
					if(preg_match('@^(src|list|dst|source|ip)([\w|\W]*)(ip|ips|address|addr)$@i', $line) || preg_match('@^\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b$@', $line)){
						return array( 0 => ";" , 1 => $column);
					}	
				}
			}
		}
	}
}
if ($argv[1] != null) {
	$extension = pathinfo($argv[1]);
	echo $extension['extension'];
}
if ($argv[2] != null) {
	$limit = $argv[2];
	echo "Custom Limit : $limit \n";
}else{
	$limit = 10;
}
switch ($extension['extension']) {
	case "xls" : 
		split_xls($argv[1], $limit);
		break;
	case "csv" : 
		split_csv($argv[1], $limit);
		break;
	case "txt" :
		split_txt($argv[1], $limit);
		break;
}


?>