<?php
error_reporting(0);
//Librerie 
//[Le standard sono reperibili su internet]
//[Le quasi-standard sono reperibili su internet, ma in una versione diversa]
//[Le Custom non sono reperibili su internet]
require_once 'lib/simple_html_dom.php'; // Quasi-Standard
require_once 'lib/PHPExcel.php'; // Standard
require_once 'geolocator/dbipObject.php'; // Custom 
require_once 'geolocator/dbip-client.class.php'; // Api 
require_once 'torchecker/BlackListChecker.php';
require_once 'torchecker/TorChecker.php';
require_once 'torchecker/whois.php';

const IP_ADDRESS_PATTERN = "@^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$@";

#### SET HERE YOUR DEFAULT IPINFO.IO API-KEY
const API_KEY = "";

function createWorkingDirs(){
    $wDirs = array("/data", "/whois");
    foreach ($wDirs as $dir){
        if(!is_dir($dir)){
            mkdir($dir);
        }
    }
}

function whoisCreate(){
    return new Whois_obj();
}

function usage(){
    $script = basename(__FILE__, '.php');
    echo "Usage: \n";
    echo "    php $script [-i <IP>] [-f <IP List file>] [-o <Output File>] [-k <API-KEY>]\n";
    echo "    -i: Single IP Address to search\n";
    echo "    -f: File with a list of IP Address to search\n";
    echo "    -o: Output file name without extension (the format is .xlsx)\n";
    echo "    -k: API Key for https://ipinfo.io (free account gives 50k monthly requests)\n";
    echo "\n";
    exit(0);
}

function allLetters(){
    return array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
}

function getRowCount($filename){
    $highestRow = 0;
    $fp = fopen($filename, 'r');
    while(!feof($fp)){
        $s = fgets($fp);
        $highestRow++;
    }
    fclose($fp);
    return $highestRow;
}

function getTorLists(){
    return array("all" => "data/tor_server_list.txt", "exit" => "data/tor_exit_list.txt");

}

function checkMultipleFiles($filename, $outputfilename=NULL, $api_key=null, $proxy=FALSE){

    //#####################################################################
    // Uncomment this and the following uncommented code snippets
    // to enable Query on TCPUTIL - Deprecated
    // $url_search = "http://www.tcpiputils.com/browse/ip-address/";
    //#####################################################################

    if($api_key === null){
        $api_key = API_KEY;
    }

    // Init Whois

    $whois = whoisCreate();

    // Counter and row limit
    $i = 1;
    $nrows = getRowCount($filename);
    /** Load $inputFileName **/
    $fp = fopen($filename, 'r');

    // Geo Ip Database Initialization
    $dbipObj = new dbipObject($api_key);

    // One row = One IP address
    $ip_info_table = array();
    while(!feof($fp)){
        // Current IP
        $ip = fgets($fp);
        $ip = trim($ip);

        printf( "(%d/%d) Analysing IP: %s ...",$i, $nrows, $ip);
        checkSingleIP($ip, $ip_info_table, $i, $api_key, $whois, $dbipObj);
        printf("%s\n", "Done");
    }
    fclose($fp);

    if($outputfilename === NULL){
        printInfoTable($ip_info_table);
    }else{
        writeResultsToFile($ip_info_table, $outputfilename);
    }
}

function checkSingleIP($ip, &$ip_info_table, &$i, $api_key=null, $whois=null, $dbipObj=null){
    if($api_key === null){
        $api_key = API_KEY;
    }

    if($whois === null){
        $whois = whoisCreate();
    }

    if ($dbipObj === null){
        $dbipObj = new dbipObject($api_key);
    }

    $i++;

    $tor_lists = getTorLists();


    if(!preg_match(IP_ADDRESS_PATTERN, $ip)){
        echo "[INVALID-IP]";
        return;
    }
    // Data cleanup (trailing spaces)

    $name_ip = "whois_";
    $name_ip .= str_replace(".","_",$ip);
    $listed = dnsbllookup($ip);
    $exit_tor_node = IsTorExitPoint($ip, $tor_lists["exit"]);
    $whois_info = $whois->whoisCall($ip);

    $result = print_r($whois_info, TRUE);
    file_put_contents("whois/$name_ip.txt", $result);

    $server_tor = IsTorServer($ip, $tor_lists["all"]);

    if(!isset($ip_info_table[0])) { $ip_info_table[0] = array("IP","Hostname","City","Region","Country","Org","Blacklist","TorNode","TorServer", "LinkToWhois"); }
    // Ensuring matrix i-th row is initialized
    if(!isset($ip_info_table[$i])){
        $ip_info_table[$i] = array();
        // Temp array
        $info = array();
        try{
            $full_dbip_info = $dbipObj->getClient()->Get_Address_Info($ip);
        }
        catch(Exception $e){
            for($i = 0; $i < 6; $i++){
                $info[$i] = "Ex: Not Found";
            }
            $full_dbip_info = null;
        }
        //print_r($full_dbip_info);
        if($full_dbip_info!=null){
            // DB Query
            foreach ($ip_info_table[0] as $k) {
                $v = strval($k);
                $v_l = strtolower($v);
                if($full_dbip_info->$v_l != null){
                    $info[$v] = $full_dbip_info->$v_l;
                }else{
                    $info[$v] = " ";
                }
            }
        }
        //print_r($info);
        $info["Blacklist"] = $listed;
        $info["TorNode"] = $exit_tor_node;
        $info["TorServer"] = $server_tor;
        if(PHP_OS === "CYGWIN"){
            $linktowhois = str_ireplace("\\cygdrive\\c", "C:", str_ireplace("/", "\\", realpath("whois/$name_ip.txt")));
        }
        else{
            $linktowhois = realpath("whois/$name_ip.txt");
        }
        $info["LinkToWhois"] = $linktowhois;

        // Matrix[i] = *Info
        $ip_info_table[$i]=$info;
    }
}

function printInfoTable($ip_info_table){
    $i = 0;
    $sep = str_repeat("=", 249);
    foreach($ip_info_table as $row){
        if($i === 1){
            printf("|%s|\n", $sep);
        }
        $i++;
        foreach ($row as $value){
            $display = utf8_decode($value);
            if(strlen($value) > 24){
                $display = substr($display, -24);
            }
            printf("|%-24s", $display);
        }
        echo "|\n";
    }
    return;
}

function writeResultsToFile($ip_info_table, $outputfilename){
    /*+--------------------------------------------------------
    //| Writing on file .xls
    //+--------------------------------------------------------
    */
    // Init PHPExcel object
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("IPgeolocatorDB")
        ->setLastModifiedBy("IPgeolocatorDB")
        ->setTitle("")
        ->setSubject("")
        ->setDescription("")
        ->setKeywords("")
        ->setCategory("");

    $r=1;

    // Looping through Matrix
    foreach($ip_info_table as $info){
        $c=0;
        // Write to XLSX
        foreach($info as $k => $v){
            // Key info --->
            //$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $letters[$c] . $r , $k);
            //$c++;

            //---> Value info
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue( allLetters()[$c] . $r , $v);
            if($k == "LinkToWhois"){
                $objPHPExcel->setActiveSheetIndex(0)->getCell( allLetters()[$c] . $r)->getHyperlink()->setUrl($info[$k]);
            }
            $c++;
        }
        $r++;
    }
    // XLSX Filename
    $objPHPExcel->getActiveSheet()->setTitle('Result');

    // Save XLSX
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $name = "$outputfilename.xlsx";
    $objWriter->save($name);
    unset($objPHPExcel);
    unset($objWriter);

};

// Input file given?
$args = getopt("i:f:o:k:h");
if($args["h"]){
    usage();
    exit(0);
}
if(is_null($args["i"]) && is_null($args["f"])){
    echo "[-] No input IP address given\n";
    usage();
    exit(1);
}
if(!is_null($args["i"]) && !is_null($args["f"])){
    echo "[-] -i and -f are mutually exclusive\n";
    usage();
    exit(1);
}

if($args["o"]){
    $outputfilename = $args["o"];
    try{
        $fp = fopen($outputfilename, "w");
        fclose($fp);
    } catch (Exception $e){
        echo "[-] Failed getting handle to outfile, check the path provided";
        exit(1);
    }
} else {
    $outputfilename = null;
}

$api_key = null;

if(isset($args["k"])){
    $api_key = $args["k"];
}

try{
    createWorkingDirs();
}catch (Exception $e){
    die("[-] Error preparing Working Directories: $e");
}

if(is_null($args["i"])){
    // Full Path
    $full_path = realpath($args["f"]);
    if(!file_exists($full_path)){
        echo("[-] File not found");
        usage();
        exit(1);
    }

    checkMultipleFiles($full_path, $outputfilename, $api_key);

}else{
    $ip = trim($args["i"]);
    printf( "[*] Analysing IP: %s ...", $ip);
    if(!preg_match(IP_ADDRESS_PATTERN, $ip)){
        echo "[-] Error: Invalid IP Address\n";
        exit(1);
    }
    $i = 1;
    $ip_info_table = array();
    checkSingleIP($ip,$ip_info_table,$i, $api_key, null, null);
    printf( "%s\n", "Done");
    printInfoTable($ip_info_table);
}

?>