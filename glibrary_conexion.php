<?php

require "glibrary.class.php";

session_start();

global $glibrary;
$certKey = '/etc/grid-security/hostcertkey.pem';
$caPath = '/etc/grid-security/certificates';
//$userName='jsevilla';
$userName = $_SESSION['login'];
//$glibrary=new GLibrary('glibrary.ct.infn.it',8822,'jsevilla','/tmp/x509up_u504','deroberto','../glibrary/classes/certificates');
$glibrary=new GLibrary('glibrary.ct.infn.it',8822,$userName,$certKey,'deroberto',$caPath);
$ident="";
$IsCollection=false;
$collID="null";

	//Connection SSL width mdclient class
	if(!$glibrary->connectSSL())
		exit("Conexion error");
	
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
		
		putPath($IsCollection);//put path by Collection or by Type
		
	}
	
	
	//variable ident, it indicates whitch value has the record
	if (isset($_GET['ident'])){
		$ident=$_GET['ident'];
		$glibrary->putPathRecord($ident);
		
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
		$glibrary->setCondition($record,$filter);
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
			filterValues($typeFilter,$IsCondition);
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
			saveVisible($nameFields,$widthFields,$IsCondition);
			break;
		case "PRUEBA":
			
			echo json_encode($glibrary->prueba($ident));
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
		echo json_encode($glibrary->getValuesFilter($typeFilter));
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
		echo json_encode($glibrary->listSurls($ident));
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
		$tokArray = split("\n", $string);//convert string to array,clean characters "{ and }"
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
		if($IsCollection){
			$glibrary->putPathCollection();
		}else
			$glibrary->putPathTypes();
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
				case "Thumb":
					break;
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
		$arrFields=$glibrary->getRecordFields();
		//echo json_encode($arrFields);
		$cont=0;	
		while($cont<$i){
			
				
		  if($tmpKeys[$cont]!=""){
			$oldRecord='"'.$tmpKeys[$cont].'":"'.((string)$tmpValues[$cont]).'"';
			//strstr($oldValues,$oldRecord));
			if((strstr($oldValues,$oldRecord))==false){
				echo $oldRecord."-".$cont;//."==".$oldValues."\n";
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
	*function: write the values of the visible -record- attributs,give fields names. JSON  Format 	
	* @return void
	*/

	function recordFields(){
		global $glibrary;
		
		$glibrary->selectFieldsbyRecord();
		echo json_encode($glibrary->getRecordFields());
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
