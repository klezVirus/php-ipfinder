<?php 
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
        if (fwrite($fp, $content)) {
                fclose($fp);
        return true;
        } else {
           return false;
        }
}
	function countRows($file){
		$count=0;
		$fp = fopen($file, 'r');
		while(!feof($fp)){
			fgets($fp);
			$count++;
		}
		fclose($fp);
		return $count;
}
// Splitting functions 
function split_xls($xls, $limit = 10){
	echo $limit;
	$objPHPExcel = PHPExcel_IOFactory::load($xls);
	// prende il primo foglio nel workbook 
	// Notare che i fogli sono indicizzati da 0 
	$objWorksheet = $objPHPExcel->getSheet(0);
	//Colonna obiettivo --> Chiama la funzione 
	$column_target = getTargetColumn($objPHPExcel, $letters);
	$highestRow = $objWorksheet->getHighestRow();
	$fileNumber = ceil(($highestRow + 1)/10);
	echo $fileNumber;
	if($highestRow > (2 * $limit)){
		for($j=0; $j < $fileNumber; $j++){
			for($i=($fileNumber*$j)+1 ; $i<($fileNumber*($j+1))+1; $i++){
				echo $j . " " . $i . "\n";
				//Ritiro l'IP all' i-esima riga
				$ip = $objPHPExcel->getActiveSheet()->getCell($column_target.$i)->getValue();
				//Eseguiamo un trim per rimuovere eventuali trailing space
				trim($ip);
				file_append("input/$j.txt", $ip);
			}
		}
	}else{
		for($i=2;$i<$highestRow;$i++){
			//Ritiro l'IP all' i-esima riga
				$ip = $objPHPExcel->getActiveSheet()->getCell($column_target.$i)->getValue();
				//Eseguiamo un trim per rimuovere eventuali trailing space
				trim($ip);
				file_append("input/A.txt", $ip);
			}
		}
	}
function split_csv($csv, $limit = 10){

	$highestRow = countRows($csv);
	$fileNumber = ceil(($highestRow + 1)/10);
	$answer = getTargetField($csv);
	if($answer==NULL){
		echo "Impossibile effettuare lo splitting: Field corretto non trovato.";
		continue;
	}
	if($highestRow > 2 * $limit){
		for($j=0; $j < $fileNumber; $j++){
			for($i=($fileNumber*$j)+1 ; $i=($fileNumber*($j+1))+1; $i++){
				$line = fgetcsv($csv, $answer[0]);
				//Ritiro l'IP all' i-esima riga
				$ip = $line[$answer[1]];
				//Eseguiamo un trim per rimuovere eventuali trailing space
				trim($ip);
				file_append("input/$j.txt", $ip);
			}
		}
	}else{
		for($i=2;$i<$highestRow;$i++){
			$line = fgetcsv($csv, $answer[0]);
			//Ritiro l'IP all' i-esima riga
			$ip = $line[$answer[1]];
			//Eseguiamo un trim per rimuovere eventuali trailing space
			trim($ip);
			file_append("input/A.txt", $ip);
		}
	}
}
function split_txt($txt, $limit = 10){
	
	$highestRow = countRows($txt);
	$fileNumber = ceil(($highestRow + 1)/10);
	$fp = @fopen($txt,'r');
	if($highestRow > 2 * $limit){
		for($j=0; $j < $fileNumber; $j++){
			for($i=($fileNumber*$j)+1 ; $i=($fileNumber*($j+1))+1; $i++){
				//Ritiro l'IP all' i-esima riga
				$ip = fgets($fp);
				//Eseguiamo un trim per rimuovere eventuali trailing space
				trim($ip);
				file_append("input/$j.txt", $ip);
			}
		}
	}else{
		for($i=2;$i<$highestRow;$i++){
			//Ritiro l'IP all' i-esima riga
			$ip = $objPHPExcel->getActiveSheet()->getCell($column_target.$i)->getValue();
			//Eseguiamo un trim per rimuovere eventuali trailing space
			trim($ip);
			file_append("input/A.txt", $ip);
			}
		}

}
//variabile utile all'indicizzazione su excel
$letters = array(
	"A",
	"B",
	"C",
	"D",
	"E",
	"F",
	"G",
	"H",
	"I",
	"J",
	"K",
	"L",
	"M",
	"N",
	"O",
	"P",
	"Q",
	"R",
	"S",
	"T", 
	"U",
	"V",
	"W",
	"X",
	"Y",
	"Z"
);

//Funzione che trova la colonna di indirizzi IP nell'excel e ne restituisce l'indice
function getTargetColumn($objPHPExcel , $letters){
	$row =1; //Riga iniziale
	$column = 0; // Indice colonna iniziale
	$column_target; // Indice colonna obiettivo
	//Finché sulla prima riga c'è un valore cerco la colonna relativa agli IP
	while($objPHPExcel->getActiveSheet()->getCell($letters[$column].$row)->getValue()!=null){
		// Fetch del valore della cella
		$value = $objPHPExcel->getActiveSheet()->getCell($letters[$column].$row)->getValue();
		//Se ho trovato la colonna relativa agli IP
		if(preg_match('@([\w|\W]*)src([\w|\W]*)ip([\w|\W]*)@i', $value) || preg_match('@([\d]{1,3}).([\d]{1,3}).([\d]{1,3}).([\d]{1,3})@', $value)){
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
	$array = fgetcsv($fp);
	fclose($fp);
	$fields = count($array);
	if($fields > 1){
		for($column = 0; $column < $fields; $column++){
			$line = trim($array[$column], " _^-'\"\t\n\r\0\x0B");
			if(preg_match('@([\w|\W]*)src([\w|\W]*)ip([\w|\W]*)@i', $line) || preg_match('@([\d]{1,3}).([\d]{1,3}).([\d]{1,3}).([\d]{1,3})@', $line)){
				return array(',',$column);
			}
		}
	}
	else {
		$line = trim($array[0], " _^-'\"\t\n\r\0\x0B");
		if(preg_match('@(src|list|dst|source|ip)([\w|\W]*)([list|ip|ips|address|addr]*)@i', $line) || preg_match('@([\d]{1,3}).([\d]{1,3}).([\d]{1,3}).([\d]{1,3})@', $line)){
				return array(',',0);
		}else if(preg_match('@([\w|\W]*)src([\w|\W]*)ip([\w|\W]*)@i', $line) || preg_match('@([\w|\W]*)([\d]{1,3}).([\d]{1,3}).([\d]{1,3}).([\d]{1,3})([\w|\W]*)@i', $line)){
			$fields = count($array);
			if($fields > 1){
				for($column = 0; $column < $fields; $column++){
					$line = trim($array[$column], " _^-'\"\t\n\r\0\x0B");
					if(preg_match('@([\w|\W]*)src([\w|\W]*)ip([\w|\W]*)@i', $line) || preg_match('@([\d]{1,3}).([\d]{1,3}).([\d]{1,3}).([\d]{1,3})@', $line)){
						return array(';',$column);
					}	
				}
			}
		}
	}
	//Finché sulla prima riga c'è un valore cerco la colonna relativa agli IP
	while($objPHPExcel->getActiveSheet()->getCell($letters[$column].$row)->getValue()!=null){
		// Fetch del valore della cella
		$value = $objPHPExcel->getActiveSheet()->getCell($letters[$column].$row)->getValue();
		//Se ho trovato la colonna relativa agli IP
		if(preg_match('@([\w|\W]*)src([\w|\W]*)ip([\w|\W]*)@i', $value)){
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
if ($argv[1] != null) {
	$extension = pathinfo(argv[1]);
}
switch $extension {
	case "xls" : 
		split_xls($argv[1]);
		break;
	case "csv" : 
		split_csv($argv[1]);
		break;
	case "txt" :
		split_txt($argv[1]);
		break;
}


?>