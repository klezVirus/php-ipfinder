<?php
require_once('lib/PHPExcel.php');

function usage(){

}

$args = getopt("t:h");


$files = array();
$output = "Results.xlsx";
$files_path = "results/Risultati analisi del file _ ";
// Load spreadsheet files
$out = new PHPExcel();
$out->setActiveSheetIndex(0);

foreach(scandir($x) as $file) if (pathinfo($file)['extension'] === "xlsx") {
	
	$file = PHPExcel_IOFactory::load( $file);
	$findEndDataRow = $file->getActiveSheet()->getHighestRow();
	$findEndDataColumn = $file->getActiveSheet()->getHighestColumn();
	$findEndData = $findEndDataColumn . $findEndDataRow;
	$beeData = $file->getActiveSheet()->rangeToArray('A2:' . $findEndData);
	$appendStartRow = $out->getActiveSheet()->getHighestRow() + 1;
	$out->getActiveSheet()->fromArray($beeData, null, 'A' . $appendStartRow);
}

$head = array("IP","Hostname","City","Region","Country","Org","Blacklist","TorNode","TorServer", "LinkToWhois");
$out->getActiveSheet()->fromArray($head,null,'A1');

// Save the spreadsheet with the merged data
$objWriter = PHPExcel_IOFactory::createWriter($out, 'Excel2007');
$objWriter->save($output);

?>