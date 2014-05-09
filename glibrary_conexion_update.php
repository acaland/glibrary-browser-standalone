<?php

require "glibrary_update.class.php";

//error_log(print_r($_GET, true), 3, "/tmp/postErr.log");

session_start();

global $glibrary;

$userName = $_SESSION['login'];
if (!isset($_SESSION['login'])) {
		header("Location:login.php?error=dologin");
		exit("You have to be authenticated\n");
}
//$userName='jsevilla';

$ident="";
$IsCollection=false;
$collID="null";
$pathAMGA="";

//$glibrary=new GLibrary('glibrary.ct.infn.it',8822,'jsevilla','/tmp/x509up_u504','deroberto','../glibrary/classes/certificates');
//if (isset($_SESSION['GLibrary_conn'])) {
//	$glibrary = unserialize($_SESSION['GLibrary_conn']);
//	error_log(print_r("connession settata ".$glibrary ."\n", true), 3, "/tmp/postErr.log");
	//error_log(print_r("stampo glibrary from session\n", true), 3, "/tmp/postErr.log");
	//error_log(print_r($glibrary, true), 3, "/tmp/postErr.log");
	
//} else {
	$certKey = '/etc/grid-security/hostcertkey.pem';
	$caPath = '/etc/grid-security/certificates';
	$glibrary=new GLibrary('glibrary.ct.infn.it',8822,$userName,$certKey,'deroberto2',$caPath);
	//Connection SSL width mdclient class
	if(!$glibrary->connectSSL())
		exit("Connection error");
//	$_SESSION['GLibrary_conn'] = serialize($glibrary);
	//error_log(print_r("stampo GLibrarry_conn\n", true), 3, "/tmp/postErr.log");
	//error_log(print_r($_SESSION['GLibrary_conn'], true), 3, "/tmp/postErr.log");
	//error_log(print_r("stampo glibrary\n", true), 3, "/tmp/postErr.log");
	//error_log(print_r($glibrary, true), 3, "/tmp/postErr.log");
//}	
///////////////////////TAKE DATA FROM GET////////////////////////////
	$task = '';	//var task,this selects action
  	if (isset($_GET['task'])){
		$task = $_GET['task'];   
		
  	}
	// variable valueImg, this contens binary image's value
	if (isset($_GET['idImg'])){
		$idImg=$_GET['idImg'];		
	}
	if (isset($_GET['pathImg'])){
		$pathImg=$_GET['pathImg'];
		
	}
	
	//variable rep, this has repertory's value
	if (isset($_GET['rep'])){
				
		$glibrary->setRepository($_GET['rep']);		
	}
	
	//variable collID, id of collection
	if(isset($_GET['collID'])){		
		$collID=$_GET['collID'];
		if($collID!="null")
		     $IsCollection=true;
	}
	//variable node, this indicates nodes' value
	if (isset($_GET['node'])){
			
		$glibrary->setNode($_GET['node']);
		
		$pathAMGA=putPath($IsCollection);//put path by Collection or by Type
		
	}
	if (isset($_GET['pathEntries'])){
		$glibrary->setPath($_GET['pathEntries']);
	}
	
	//variable ident, it indicates whitch value has the record
	if (isset($_GET['ident'])){
		$ident=$_GET['ident'];
		$pathAMGA=$glibrary->putPathRecord($ident);
		
	}else $ident="";
	//variable typeF, the value of actual type Filter.
	if(isset($_GET['typeF'])){
		$typeFilter=$_GET['typeF'];
	}
	//get string of filters, changing string to array.
	if(isset($_GET['arrFilt'])){		
	///////////////////STRING FILTERS TO ARRAY FILTERS////////	
		$filterStr=$_GET['arrFilt'];
		$filterStr=str_replace(', ','##',$filterStr);
		$tok = strtok ($filterStr,",");		
		while ($tok !== false) {
			$tmp=str_replace('##',', ',$tok);
			$filter[]=$tmp;
			$tok = strtok(",");
			
		}
		
	///////////////////STRING RECORDS TO ARRAY RECORDS////////	
		$recordStr=$_GET['arrRec'];	
		$tmp1=str_replace(', ','##',$recordStr);
		$tok = strtok ($tmp1,",");		
		while ($tok !== false) {
			$tmp=str_replace('##',', ',$tok);
			$record[]=$tmp;
			$tok = strtok(",");			
		}
		selectFields($IsCollection);
		$glibrary->setCondition($record,$filter,$IsCollection);
	}
	
	//variable nameFields, keep string with visible attributs names.
	$nameFields="";
	
	if(isset($_GET['names'])){
		$nameFields=$_GET['names'];
		
	}
	//variable withFields, keep string with visible attributs widths.
	$widthFields="";
	if(isset($_GET['widths'])){
		$widthFields=$_GET['widths'];
	}
	
///////////////////////SELECT FUNCTION/////////////////////////	
  	switch($task){
		
		case "HIDDENFIELD":
			
			hiddenFields($IsCollection);//Give the values of VisibleAtrrs.
			break;	
		
		case "RECORDFIELD":	//Give the values of VisibleAtrrs.
			recordFields();
			break;
		case "LISTRECORDS"://list select record or all records
		
			//error_log(print_r($_GET, true), 3, "/tmp/postErr.log");	
			//error_log(print_r("post\n", true), 3, "/tmp/postErr.log");
			//error_log(print_r($_POST, true), 3, "/tmp/postErr.log");
			listRecord($ident,$IsCollection);
			break;
		case "LISTSURL"://list entry surl
			listSurl($ident);
			break;
		case "LISTRELATION":
			listRelation($ident);
			break;
		case "UPDATE"://save changed data from formulary
			saveData();			
			break;
		case "GETUSER"://get actual name user
			getUser();
			break;
		case "GETFILTER"://get attributs for to filter
			filterAttributs($IsCollection);
			break;	
		case "GETFILTERATTRI"://get values of filter attributs
			filterValues($typeFilter,$IsCollection);
			break;	
		case "GETTREETYPES"://get tree nodes, with collection data
			treeTypes();
			break;
		case "GETTREECOLLECT"://get tree nodes, with collection data
			treeCollection();
			break;
		case "GETTHUMB"://get image from binary image to jpg image
			
			$glibrary->getThumbnail($idImg);
			
			break;
		case "SAVEVISUAL":	
			saveVisible($nameFields,$widthFields,$IsCollection);
			break;
		case "GETSE":
			
			echo json_encode($glibrary->getStorageElements());
			break;	
		case "SETENTRY":
			saveEntry();
			break;
		case "SETPATH":			
			echo $pathAMGA;			
			break;
		case "PRUEBA":
			//echo 'hola';
			//echo '"'.$pathAMGA.'"';
			//echo json_encode($glibrary->putPathTypes());
			break;
		default:
      			echo "{failure:true}";  // Simple 1-dim JSON array to tell client the request failed.
			break;
  	}

////////////////////////////////////////////////////////////////
//////////////////////////FUNCTIONS/////////////////////////////
	
	
	/**
	*function: write the values of the hidde attributs, give fields names. JSON Format 	
	* @return void
	*/
	function hiddenFields($IsCollection){
		global $glibrary;
		
		selectFields($IsCollection);
		echo json_encode($glibrary->getHiddenFields());
		
	}	
	
	
	/**
	*function: Write name user in JSON format	
	* @return void
	*/	
	function getUser(){
		global $glibrary;
		echo json_encode($glibrary->getProxyUser());
	}
	
	/**
	*function: get attributs for to filter the entry. Write values in JSON format	
	* @return void
	*/
	function filterAttributs($IsCollection){
		global $glibrary;
		
		if($IsCollection){
			
			echo json_encode($glibrary->getFilters("/CollectionTree","Name"));
		}
		else
			echo json_encode($glibrary->getFilters("/Types","TypeName"));
	}
	
	/**
	*function: get values of filter attributs. Write values in JSON format	
	* @return void
	*/
	function filterValues($typeFilter,$IsCollection){
		global $glibrary;
		selectFields($IsCollection);
		echo json_encode($glibrary->getValuesFilter($typeFilter,$IsCollection));
	}
	
	/**
	*function: list select record or all records. If $ident is empty list all record. Write values in JSON format	
	* @return void
	*/
	function listRecord($ident,$IsCollection){
		global $glibrary;		
		
		//selectFields($IsCollection);
		echo"{response:{   status:0,data:";				
		if($ident==""){
			if($IsCollection){				
			   echo json_encode($glibrary->listCollection());
			}else{				
			   echo json_encode($glibrary->listAllSelectRecords());
			}
		}else{
			$glibrary->selectFieldsbyRecord();
			echo json_encode($glibrary->getSelectRecord($ident));
		}
		echo"}}";
	}
	/**
	*function: list entry surl, entry with ID like $ident. Write values in JSON format	
	* @return void
	*/
	function listSurl($ident){
		global $glibrary;
		
		$list_surl = $glibrary->listSurls($ident);
		//error_log(print_r($_SESSION['guest'], true), 3, "/tmp/postErr.log");
		//error_log(print_r($list_surl, true), 3, "/tmp/postErr.log");
		$filename = $glibrary->getEntryFilename($ident);
		//error_log(print_r($filename, true), 3, "/tmp/postErr.log");
		if ($_SESSION['guest']) {
			
			for ($i = 0; $i < count($list_surl); $i++) {
				$list_surl[$i]['name'] = "guestdownload.php?filename=" . urlencode($filename) . "&surl=" . urlencode($list_surl[$i]['name']);
			}
		}
		echo json_encode($list_surl);
		
	}
	
	/**
	*function: list entry surl, entry with ID like $ident. Write values in JSON format	
	* @return void
	*/
	function listRelation($ident){
		global $glibrary;
		echo json_encode($glibrary->listRelations($ident));
	}
	/**
	*function: parse a string from smartclient to array from glibrary.class : '{"dato1","dato2"}'	
	* @return void
	*/
	function parseArray($string){
		$tokArray = split("\n|##", $string);//convert string to array,clean characters "{ and }"
		$long=sizeof($tokArray);
		$arrayParsed='{';
		for ($a = 0,$b = $long-1;$a < $b;$a++){
			$arrayParsed=$arrayParsed.'"'.$tokArray[$a].'",';
		}
		$arrayParsed=$arrayParsed.'"'.$tokArray[$b].'"';
		$arrayParsed=$arrayParsed.'}';
		return $arrayParsed;
		//echo json_encode($arrayParsed);
	}
	/**
	*function: call the correct method to put the path where exam entries. collection or types path.
	* @return void
	*/
	function putPath($IsCollection){
		global $glibrary;
		$path;
		if($IsCollection){
			
			$path=$glibrary->putPathCollection();
		}else
			$path=$glibrary->putPathTypes();
		return $path;
	}
	
	/**
	*function: write the values of the visible -record- attributs,give fields names. JSON  Format 	
	* @return void
	*/

	function recordFields(){
		global $glibrary;
		
		$glibrary->selectFieldsbyRecord();
		echo json_encode($glibrary->getRecordFields());
	}
	
	/**
	*function: save changed data from formulary sent by POST. write new record in JSON format.
	* @return void
	*/
	function saveData(){
		global $glibrary;
		$number = count($_POST);
		$tags = array_keys($_POST);
		$values = array_values($_POST);
		$i=0;
		$find=false;
		$IsCollection=false;
		
		while(($i<$number)&&(!$find)){		
			
			switch($tags[$i]){
				case "_operationType":
					$oldValues=$values[$i+1];
					$find=true;
					break;
				case "node":
					$glibrary->setNode($values[$i]);
					break;
				case "repository":
					$glibrary->setRepository($values[$i]);
					break;
				case "collID":
					//$glibrary->setNode($values[$i]);
					if($values[$i]!="null")
					   $IsCollection=true;
					break;
				case "FILE":
					$file=$values[$i];
					
					break;
				case "OWNER":
					break;
				case "PERMISSIONS":
					break;
				case "GROUP_RIGHTS":				
					break;
				case "surl":				
					break;			
				//case "Thumb":
				//	break;
			default:
				$tmpValues[]=$values[$i];			
				$tmpKeys[]=$tags[$i];
				
			break;
		}
		$data[$tags[$i]]=$values[$i];
		$arr[]=$data;
		$data=null;
		$i++;
		}
		$glibrary->putPathRecord($file);
		$glibrary->selectFieldsbyRecord();
		//$arrFields=$glibrary->getVisibleFields();
		$arrFields=$glibrary->getRecordFields();
		//echo json_encode($arrFields);
		$cont=0;	
		while($cont<$i){
			
		  if($tmpKeys[$cont]!=""){
			$oldRecord='"'.$tmpKeys[$cont].'":"'.((string)$tmpValues[$cont]).'"';
			//strstr($oldValues,$oldRecord));
			if((strstr($oldValues,$oldRecord))==false){
				//echo $oldRecord."-".$cont;//."==".$oldValues."\n";
				if($glibrary->IsArray($tmpKeys[$cont],$arrFields)){				
					$arrValues[]=parseArray($tmpValues[$cont]);					
				}else
					$arrValues[]=$tmpValues[$cont];
				$arrKeys[]=$tmpKeys[$cont];
				
				
			
			}//else echo $cont;
		  }
			$cont++;
		}
		
		
		
		$glibrary->setDataEntry($file,$arrKeys,$arrValues);
		
		echo"{response:{   status:0,data:";
				echo json_encode($arr);
		echo"}}";
	
	}
	function saveEntry(){
		global $glibrary;
		$number = count($_POST);
		$tags = array_keys($_POST);
		$values = array_values($_POST);
		$i=0;
		
		$cont=0;
		$arr=array();
		while(($i<$number)&&(!$find)){	
						
			switch($tags[$i]){
				case "repository":
					$glibrary->setRepository($values[$i]);
					break;
				case "pathEntries":					
					$glibrary->setPath($values[$i]);
					$glibrary->selectFieldsbyRecord();
					$arrFields=$glibrary->getRecordFields();
										
					break;
				case "specific":
				//	echo "hola";
					$arr=jsonObjectToArray($values[$i],$arr,$arrFields);										
						
					break;
			
				case "generic":
					$arr=jsonObjectToArray($values[$i],$arr,$arrFields);
		
					break;
				
				case "replicas":
					//echo $values[$i];
					$replicas=objectToArray($values[$i]);
					break;
				case "collections":				
					$coll=objectToArray($values[$i]);
					break;
				default:
					//$tmpValues[]=values[$i];			
					//$tmpKeys[]=$tags[$i];
				
				break;
			}
			//echo $tags[$i].":".$values[$i]."\n";
			$i++;		
		}
		$glibrary->setEntry($arr["key"],$arr["value"],$replicas,$coll);
		//echo json_encode($arr["value"]);
		//echo json_encode($arr["key"]);
		//echo json_encode($replicas);
		//echo json_encode($coll);
	}
	
	
	function objectToArray($values){
		//$tmp=trim($values);
		global $glibrary;
		//$title1=str_replace(', ','##',$title);
		$tokArray = split('{|:"|",|}', $values);//convert string to array,clean characters "{ and }"
		$data="";
		$long=sizeof($tokArray);
		for ($a = 1,$b = $long-1;$a < $b;$a++){
			$tmp=$tokArray[$a];
			//not take null values	
			$cadNull=strstr($tmp,"null");
			if($cadNull!=false)			
				$tmp=substr($cadNull,5);				
			//////////////////////////
			$tmp=trim($tmp," \t\r\"");//clean white bad caracters
			$parsedArray[]=$tmp;
		}
		return $parsedArray;
	}
	
	function jsonObjectToArray($values,$parsedArray,$arrFields){
		//$tmp=trim($values);
		global $glibrary;
		//$title1=str_replace(', ','##',$title);
		$tokArray = split('{|:"|",|}', $values);//convert string to array,clean characters "{ and }"
		$data="";
		$long=sizeof($tokArray);
		for ($a = 1,$b = $long-1;$a < $b;$a++){
			$tmp=$tokArray[$a];
			//not take null values	
			$cadNull=strstr($tmp,"null");
			if($cadNull!=false)			
				$tmp=substr($cadNull,5);				
			//////////////////////////
			$tmp=trim($tmp," \t\r\"");//clean white bad caracters
			if($a%2!=0){
				if($tmp!="")
					$parsedArray["key"][]=$tmp;
				
			}else{
				$long=sizeof($parsedArray["key"]);
				if($glibrary->IsArray($parsedArray["key"][$long-1],$arrFields)){
					$tmp=str_replace('\n','##',$tmp);
					$tmp=parseArray($tmp);
				}
				
				$parsedArray["value"][]=$tmp;
				
			}
			
		}
		
		return $parsedArray;
	}
	
	/**
	*function: save data width regard to visible attributs. Names, titles and width values.
	* @return boolean.false if error
	*/
	function saveVisible($nameFields,$widthFields,$IsCollection){	
		global $glibrary;
		
		if(($nameFields!="")&&($widthFields!="")){	
			$arrKeys[]="VisibleAttrs";
			$arrKeys[]="ColumnWidth";
			$arrValues[]=$nameFields;
			$arrValues[]=$widthFields;
			if($IsCollection)
				$glibrary->setDataCollections($arrKeys,$arrValues);
			else
				$glibrary->setDataTypes($arrKeys,$arrValues);
			echo "Save successfully";
			return true;
		}
		echo "error";
		return false;		
	}
	/**
	*function: call the correct method, to put Fields in datasources.Collection fields or Types Fields
	* @return void
	*/
	function selectFields($IsCollection){
		global $glibrary;
		if($IsCollection){
			$glibrary->selectFieldsbyCollections();
		}
		else
			$glibrary->selectFieldsbyTypes();
			
	}	
	/**
	*function: get tree nodes with collection data. Write values in JSON format
	* @return void
	*/
	function treeTypes(){
		global $glibrary;
		echo json_encode($glibrary->getTreeTypes());
	}
	
	
	/**
	*function: get tree nodes with collection data. Write values in JSON format
	* @return void
	*/
	function treeCollection(){
		global $glibrary;
		echo json_encode($glibrary->getTreeCollection());
	}
	
	/**
	*function: write the values of the visible attributs,give fields names. JSON  Format 	
	* @return void
	*/

	/*function visibleFields($IsCollection){
		global $glibrary;
		
		//selectFields($IsCollection);
		$glibrary->selectFieldsbyRecord();
		echo json_encode($glibrary->getRecordFields());
	}*/
?>
