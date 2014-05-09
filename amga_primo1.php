<?php
	
	
	require "mdclient.php";
	
	$AMGA_HOST = 'glibrary.ct.infn.it';
	$AMGA_PORT = 8822;
	//$login = 'jsevilla';
	$login = 'root';
	//$proxy_file = '/tmp/x509up_u504';
	$certkey = '/etc/grid-security/hostcertkey.pem';
	$capath = '/etc/grid-security/certificates';
	$arrayFields;
	$path="";
	$node;
	
	$repository;
	
	
	
	try {
			$client = new MDClient($AMGA_HOST, $AMGA_PORT, $login);
			//$client->requireSSL($proxy_file, $proxy_file, '../glibrary/classes/certificates');
			$client->requireSSL($certkey, $certkey, $capath);
			$client->connect();
	} catch (Exception $e) 	{
		echo 'Unable to connect to AMGA: ',  $e->getMessage(), "\n";
	}
	
	$client->sudo('jsevilla');
	
	
	$task = '';
  	if (isset($_GET['task'])){
		$task = $_GET['task'];   
		
  	}	
	if (isset($_GET['rep'])){
		$repository = $_GET['rep'];
	}
	if (isset($_GET['node'])){
		$node=$_GET['node'];	
		getPath($client);
	}
	
	if (isset($_GET['ident'])){
		$ident=$_GET['ident'];
		
	}else $ident="";
	
	
  	switch($task){
		/*case "LISTING":              // Give the list of the root collection
      			getList($client);
			break;*/
		case "GETATTRI":		//Give the attributs, FileName and Description
			getAttri($client);
			break;
		case "GETTREE":
			getTree($client);
			break;
		case "GETFILTER":
			getFilters($client);
			break;
		case "LISTGRID":
			getListGrid($client); 	//Give the values a set attribus of particular entry, to listgrid
			break;
		case "GETFIELD":
			getFields($client,true);//Give the values of VisibleAtrrs.
			break;
		case "HIDEFIELD":
			
			getHideFields($client,true);//Give the values of VisibleAtrrs.
			
			break;
		
		case "VISIBLEFIELD":
			
			getShowFields($client,true);//Give the values of VisibleAtrrs.
			
		break;
			
		case "LISTVIEWER":
			getListViewer($client);
			break;
		case "LISTSURL":
			
			getListSurl($client,true);
			break;
		case "UPDATE":
			setData($client);
			break;
		case "GETUSER":
			getUser($client);
			break;
		default:
      			echo "{failure:true}";  // Simple 1-dim JSON array to tell client the request failed.
			break;
  	}
	
	/////////////////////////////////////
	////////Return user/////////////////
	function getUser($cl){
		$data["name"]=$cl->whoami();				
		echo json_encode($data);
	}
	////////////////////////////////////////////////////////
	// give list about 
	//internal calling to getFields()
	function getListGrid($cl){
		global $node;
		global $repository;
		global $arrayFields;
		global $path;
		$condition="";
		$record[0]=$_GET['recor1'];
		$record[1]=$_GET['recor2'];
		$record[2]=$_GET['recor3'];
		$record[3]=$_GET['recor4'];
		$filter[0]=$_GET['typeF1'];
		$filter[1]=$_GET['typeF2'];
		$filter[2]=$_GET['typeF3'];
		$filter[3]=$_GET['typeF4'];
		//Get the path for the entries
		
		///get the atributs to show.
		getHideFields($cl,false);
		
		if(sizeof($arrayFields)==0){
			exit('null');
		}
			$attrs[0]=$path[0].":".$arrayFields[0];
			//echo $attrs[0]." ";			
			for ($a = 1,$b = sizeof($arrayFields);$a < $b;$a++){
				$attrs[]=$arrayFields[$a];
				//echo $attrs[$a]." ";				
			}
			
			
		
		//show all records
		//First filter no activate
		/*if(($filter[0]!="--")and($record[0]!="--")and($record[0]!="ALL")){
			$condition=$condition.$filter[0].'="'.$record[0].'"';
		}
		if(($filter[1]!="--")and($record[1]!="--")and($record[1]!="ALL")){
			$condition=$condition.' and '.$filter[1].'="'.$record[1].'"';
		}
		if(($filter[2]!="--")and($record[2]!="--")and($record[2]!="ALL")){
			$condition=$condition.' and '.$filter[2].'="'.$record[2].'"';
		}
		if(($filter[3]!="--")and($record[3]!="--")and($record[3]!="ALL")){
			$condition=$condition.' and '.$filter[3].'="'.$record[3].'"';
		}
		$condition=$condition.' distinct ';
		//echo $condition;
		*/
		if(($filter[0]=="--")||($record[0]=="--")||($record[0]=="-No available-")){		    		    
		    $condition='distinct';			
		}else{
		    if(($filter[1]=="--")||($record[1]=="--")||($record[1]=="-No available-")){
			   $condition=$filter[0].'="'.$record[0].'" distinct ';
				
			}else{
				if(($filter[2]=="--")||($record[2]=="--")||($record[2]=="-No available-")){
					$condition=$filter[0].'="'.$record[0].'" and '.$filter[1].'="'.$record[1].'" distinct ';
				}else{
					if(($filter[3]=="--")||($record[3]=="--")||($record[3]=="-No available-")){
						$condition=$filter[0].'="'.$record[0].'" and '.$filter[1].'="'.$record[1].'" and '.$filter[2].'="'.$record[2].'" distinct ';
					}else
						$condition=$filter[0].'="'.$record[0].'" and '.$filter[1].'="'.$record[1].'" and '.$filter[2].'="'.$record[2].'" and '.$filter[3].'="'.$record[3].'" distinct ';
						
					
				}
			}
		}
		
		$result = $cl->selectAttr($attrs,$condition);
		if(!$cl->eot()){
		  while (!$cl->eot()) {
			for ($a = 0,$b = sizeof($arrayFields);$a < $b;$a++){
				$title=$cl->getSelectAttrEntry();
				
				$data[$arrayFields[$a]]=$title[0];
				
			}
			$arr[]=$data;			
		  }		  
		  //select return nothing
		}else{
			$data["title"]="";
			$data["publication"]="";
			$data["format"]="";
			$arr[]=$data;
		}
		echo"{response:{   status:0,data:";
				echo json_encode($arr);
		echo "}}";
	
	}
/////////////////////////////////////////////////////////////////////////////
//////////////////////FUNCTION////////////////////////////////////////////////

	function getTree($cl) {
		
		global $repository;
		//$repository = $_GET['rep'];
		$path = "/" . $repository . "/Types";
		$cl->getattr($path, array("TypeName","ParentID"));
		//echo "soy aqui: ". $path;	
		while (!$cl->eot()) {
			$row = $cl->getEntry();
			$data["name"]=$row[1][0];
			$data["id"]=$row[0];
			$data["parentID"]=$row[1][1];
			$data["isFolder"]="false";
			$arr[]=$data;
			
			
		}
		
		echo json_encode($arr);
		
	}
/////////////////////////////////////////////////////////////////////////////
/////////////Function to return filters of a entry///////////////////////////

	
	function getFilters($cl){		
		global $node;
		global $repository;
		$path="/".$repository."/Types";
		$path="/".$repository."/Types";
		$attrs=array($path.":FilterAttrs","FilterLabels");
		$condition='TypeName="'.$node.'" distinct ';
		$result= $cl->selectAttr($attrs,$condition);
		
		
		if (!$cl->eot()) {
			$filter = $cl->getSelectAttrEntry();
			$titlesColums=$cl->getSelectAttrEntry();
		}
		
		if($filter[0]==""){
			   $data["filter"]="--";
			   $arr[]=$data;
		}else{
			///////////////GET NOME's Filters
			//echo $titlesColums[0];
			$tok = strtok ($titlesColums[0],",");		
			while ($tok !== false) {
			
				$tokTitle[]=$tok;
				$tok = strtok(",");	
				
			}
			
			/////////GET FILTERS
		   	$tok = strtok ($filter[0]," ");
			$i=0;
			while ($tok !== false) {
			  
			   $data["filter"]=$tok;
			   ////set title filter, it's displayed
			  if($tokTitle[$i] !=""){
				   $data["title"]=$tokTitle[$i];//title
				   
			   }else
			   	$data["title"]=$tok;
				
			   $tok = strtok(" \n\t");
			  $arr[]=$data;
			  $i++;
			}
		}
		  
		
		echo json_encode($arr);
		
	} 

/////////////////////////////////////////////////////////////////////////////
//////////////////////GET ATRRIBUT TO FILTER//////////////////////////////////

	function getAttri($cl){
		global $node;
		global $repository;
		global $path;
		//$node=$_GET['node'];
		$typeFilter=$_GET['typeF'];
		//$repository = $_GET['rep'];
		
		//Get the path for the entries
		
		
		$attrs=array($path[0].":".$typeFilter);
///////////////////////DISCRIMINAR FILTROS CON RESPECTO AL ANTERIOR///////////////////
		$record[0]=$_GET['recor1'];
		$record[1]=$_GET['recor2'];
		$record[2]=$_GET['recor3'];
		$record[3]=$_GET['recor4'];
		$filter[0]=$_GET['typeF1'];
		$filter[1]=$_GET['typeF2'];
		$filter[2]=$_GET['typeF3'];
		$filter[3]=$_GET['typeF4'];
		
		if(($filter[0]=="--")||($record[0]=="--")||($record[0]=="-No available-")){		    		    
		    $condition='distinct';			
		}else{
		    if(($filter[1]=="--")||($record[1]=="--")||($record[1]=="-No available-")){
			   $condition=$filter[0].'="'.$record[0].'" distinct ';
				
			}else{
				if(($filter[2]=="--")||($record[2]=="--")||($record[2]=="-No available-")){
					$condition=$filter[0].'="'.$record[0].'" and '.$filter[1].'="'.$record[1].'" distinct ';
				}else{
					if(($filter[3]=="--")||($record[3]=="--")||($record[3]=="-No available-")){
						$condition=$filter[0].'="'.$record[0].'" and '.$filter[1].'="'.$record[1].'" and '.$filter[2].'="'.$record[2].'" distinct ';
					}else
						$condition=$filter[0].'="'.$record[0].'" and '.$filter[1].'="'.$record[1].'" and '.$filter[2].'="'.$record[2].'" and '.$filter[3].'="'.$record[3].'" distinct ';
						
					
				}
			}
		}
	///////////////////////////////////////////	
		
		$result = $cl->selectAttr($attrs,$condition);
		//$data["attr"]="ALL";
		//$arr[]=$data;
		
		if(!$cl->eot()){
		  while (!$cl->eot()) {
			$genre = $cl->getSelectAttrEntry();
			if($genre[0]!=""){
				$data["attr"]=$genre[0];			
				$arr[]=$data;
			}else{
				//$data["attr"]="--";
				$data["attr"]="-No available-";
				$arr[]=$data;
			}
		  }
		}else{
			$data["attr"]="-No available-";
			$arr[]=$data;
		}		
		  echo json_encode($arr);
		
	
	}
/////////////////////////////////////////////////////////////////////////////
//////////////////////FUNCTION RETURN NAMES FIELDS//////////////////////////
	
	///////////////////////////////
	//ListGrid. Visible attributs//
	function getFields($cl,$flag){
		
		global $node;
		global $arrayFields;
		global $repository;
		
		$path="/".$repository."/Types";
		
		$attrs=array($path.":VisibleAttrs","ColumnLabels");
		$condition='TypeName="'.$node.'" distinct ';
		//echo $repository;
		$result= $cl->selectAttr($attrs,$condition);
		
		while (!$cl->eot()) {
			$filter = $cl->getSelectAttrEntry();
			$titlesColums=$cl->getSelectAttrEntry();
		}
		
		///////////////get Title's fields
		$tok = strtok ($titlesColums[0],",");		
		while ($tok !== false) {
			
			$tokTitle[]=$tok;
			$tok = strtok(",");			
		}
		/////////////get name's fields
		$tok = strtok ($filter[0]," ");
		
		//it is necesary, because datasource doesn't support empty file
		if($tok == ""){
			$data["name"]="Colums";
			$data["type"]="List Empty. Select type";
			$arr[]=$data;
			$arrayFields[]=$tok;
		}else{
			$data["name"]="FILE";
			//$data["visible"]=0;
			$arr[]=$data;
			$arrayFields[]="FILE";
		}
		
		$i=0;
		while ($tok !== false) {
			 
		   $data["name"]=$tok;
		   if($tokTitle[$i] !=""){
		   	$data["title"]=$tokTitle[$i];//title
			
		   }
		   
		   $arr[]=$data;		   
		   $arrayFields[]=$tok;		   
		   $tok = strtok(" ");
		   $i++;
		}
		
		//Flag false-> calling from LISTGRID event.
		if($flag){			
			echo json_encode($arr);
		}
		
	} 	
////////////////////////////////////////////////////////////////////
	function getShowFields($cl,$flag){
		
		global $node;
		global $arrayFields;
		global $repository;
		global $path;
		
		//$path="/".$repository."/Entries"."/".$node;
		
		$data["name"]="FILE";
		$arrayFields[]="FILE";
		$data["showIf"]="false";
		$data["primaryKey"]="true";				
		$arr[]=$data;
		
		
		
		list($atrrs,$types)=$cl->listAttr($path[0]);
		$atrrs = array_merge($atrrs, array("OWNER","PERMISSIONS", "GROUP_RIGHTS"));
		
		$arr[]=$data;
		for ($a = 0,$b = sizeof($atrrs);$a < $b;$a++){
			
			$data["name"]=$atrrs[$a];
			$data["type"]=$types[$a];
			$data["showIf"]="true";
			$arrayFields[]=$atrrs[$a];
			$arr[]=$data;	
		}	
	
		if($flag){			
			echo json_encode($arr);
		}
		 	//echo json_encode($arr);
	}	
	/////////////////////////////
	//Viewer. All atribbuts/////
	function getHideFields($cl,$flag){
		
		global $node;
		global $arrayFields;
		global $repository;
		global $path;
		
		//$path="/".$repository."/Entries"."/".$node;
		///////////////////////////////////////////////////////////////
		$pathVisible="/".$repository."/Types";
		
		$attrs=array($pathVisible.":VisibleAttrs","ColumnLabels","ColumnWidth");
		$condition='TypeName="'.$node.'" distinct ';
		//echo $repository;
		$result= $cl->selectAttr($attrs,$condition);
		
		if (!$cl->eot()) {
			$filter = $cl->getSelectAttrEntry();
			$titlesColums=$cl->getSelectAttrEntry();
			$widthColums=$cl->getSelectAttrEntry();
			$rabbish=$cl->getSelectAttrEntry();
		}
		
		///////////////////EXTRACT COLUMLABELS/////////////////////////////////////
		$tok = strtok ($filter[0]," ");		
		while ($tok !== false) {
			
			$tokvisibleAt[]=$tok;
			$tok = strtok(" ");			
		}
		///////////////////EXTRACT COLUMWIDTH/////////////////////////////////////
		$tok = strtok ($widthColums[0]," ");		
		while ($tok !== false) {
			
			$tokWidth[]=$tok;
			$tok = strtok(" ");			
		}
		///////////////////EXTRACT COLUMLABELS/////////////////////////////////////
		$tok = strtok ($titlesColums[0],",");		
		while ($tok !== false) {
			
			$tokTitle[]=$tok;
			$tok = strtok(",");			
		}
		$contTitle=0;
		$contWidth=0;
		//////////////////////////ADD "FILTER"////////////////////////////////
		
		$data["name"]="FILE";
		$arrayFields[]="FILE";
		$data["showIf"]="true";
		$data["primaryKey"]="true";				
		$arr[]=$data;
		
		
		for ($a = 0,$b = sizeof($tokvisibleAt);$a < $b;$a++){
			$data["name"]=$tokvisibleAt[$a];
			$data["showIf"]="true";
			//$data["type"]=$types[$a];
			if($tokTitle[$a] !=""){
				$data["title"]=$tokTitle[$a];					
				
			}
			if($tokWidth[$a] !=""){
					$data["width"]=$tokWidth[$a];
					
			}
			$arrayFields[]=$tokvisibleAt[$a];
			$arr[]=$data;
			
		}
		
		
		///////////////////////LISTATTRS//////////////////////////////////////		
		list($atrrs,$types)=$cl->listAttr($path[0]);
		$atrrs = array_merge($atrrs, array("OWNER","PERMISSIONS", "GROUP_RIGHTS"));
		for ($a = 0,$b = sizeof($atrrs);$a < $b;$a++){
			
			
			//$data["title"]="hola";
			if((strstr($filter[0],$atrrs[$a]))==false){
				$data["name"]=$atrrs[$a];
				$data["type"]=$types[$a];
				$data["showIf"]="false";		
				$arrayFields[]=$atrrs[$a];
				$arr[]=$data;
			}/*else {///Set visible and title
				$data["showIf"]="true";
				if($tokTitle[$contTitle] !=""){
					$data["title"]=$tokTitle[$contTitle];
					//$data["width"]=$tokWidth[$contTitle];
					$contTitle++;
				}
				if($tokWidth[$contWidth] !=""){
					$data["width"]=$tokWidth[$contWidth];
					$contWidth++;
				}
				
				
			}*/
			
			//$data["showIf"]="false";
				
		}	
	
		
		if($flag){			
			echo json_encode($arr);
		}
		 	//echo json_encode($arr);
	}	
	
	
	/////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////	
	function getListViewer($cl){
		global $node;
		global $repository;
		global $arrayFields;
		global $path;
		global $ident;
		global $surlGlobal;
		//
		
		//Get the path for the entries
	
		
		
		///get the atributs to show.
			
	if($ident==""){
		
		getListGrid($cl);
	}else{
		getHideFields($cl,false);
		if(sizeof($arrayFields)==0){
			exit('null');
		}
			$attrs[]=$path[0].":".$arrayFields[0];
					
			for ($a = 1,$b = sizeof($arrayFields);$a < $b;$a++){
				$attrs[]=$arrayFields[$a];
				//echo $arrayFields[$a]." ";
			}
			
		$condition='FILE="'.$ident.'" distinct ';
		$result = $cl->selectAttr($attrs,$condition);
			
		if(!$cl->eot()){
			
		  while (!$cl->eot()) {
			for ($a = 0,$b = sizeof($arrayFields);$a < $b;$a++){
				$info=$cl->getSelectAttrEntry();
				//echo "hola";
				$data[$arrayFields[$a]]=$info[0];
				//echo($info[0]);
				
			}
				$arr[]=$data;	
		  }
		}
		  
		  
		  	  
	/////////Query SURL/////////////////////
		
		 
		echo"{response:{   status:0,data:";
		echo json_encode($arr);
		
		echo"}}";
	}
}
	
///////////////////////////////////////////////////////////
/////////////////////////SURL/////////////////////////////
function getListSurl($cl,$flag){
		
		global $node;
		global $repository;
		global $surlGlobal;
		global $ident;
		$pathSURL="/".$repository."/Replicas";
		$conditionSURL='ID="'.$ident.'"';
		$attrsSURL=array($pathSURL.":surl");
		
		$cl->selectAttr($attrsSURL,$conditionSURL);
		if(!$cl->eot()){
		while (!$cl->eot())
		{
			$tmp= $cl->getSelectAttrEntry();
			
			$tmp[0] = str_replace( "srm://", "https://", $tmp[0]);
			$tmp[0] = str_replace( "gsiftp://", "https://", $tmp[0]);
			
			$data["name"]=$tmp[0];
			$data["type"]="link";
			$surl[] = $data;
			$surlGlobal[]=$tmp[0];//$tmp[0];
		}
		}else{
			$data["title"]="";
			$data["publication"]="";
			$data["format"]="";
			$arr[]=$data;
		}
		
		if($flag){
			echo json_encode($surl);
		}
}
/////////////////////////////////////////////////////////
//////////////// UPDATE ATTRIBUTS////////////////////////
////////////////////////////////////////////////////////
function setData($cl){
	global $node;
	global $repository;
	global $path;
	//get attributs from POST.
	$number = count($_POST);
	$tags = array_keys($_POST);
	$values = array_values($_POST);
	$i=0;
	$find=false;
	
	while(($i<$number)&&(!$find)){		
		
		switch($tags[$i]){
			case "_operationType":
				$oldValues=$values[$i+1];
				$find=true;
			break;
			case "node":
				$node=$values[$i];
				break;
			case "repository":
				$repository=$values[$i];
				break;
			case "FILE":
				$file=$values[$i];
				break;
			case "OWNER":
				break;
			case "PERMISSIONS":
				break;
			case "GROUP_RIGHTS":
				//echo $values[$i];
				break;
			case "surl":
				
				break;
			
			case	"Thumb":
				break;
			default:
				$tmpValues[]=$values[$i];			
				$tmpKeys[]=$tags[$i];
				
			break;
		
		}
		$data[$tags[$i]]=$values[$i];
		$i++;
		
	}
	$arr[]=$data;
	$cont=0;
	//echo $oldValues;
	while($cont<$i){
		$oldRecord=$tmpKeys[$cont].":".'"'.((string)$tmpValues[$cont]).'"';
		
		if((strstr($oldValues,$oldRecord))==false){
			$arrKeys[]=$tmpKeys[$cont];
			$arrValues[]=$tmpValues[$cont];
			echo $oldRecord;
		}//else echo $cont;
		$cont++;
	}
	
	//Get the path for the entries	
	
	getPath($cl);
	
	$pathSave=$path[0]."/".$file;
	
	$cl->setAttr($pathSave, $arrKeys, $arrValues);
	
	echo"{response:{   status:0,data:";
	
	echo json_encode($arr);
	echo"}}";
	
}

function getPath($cl){
		global $node;
		global $repository;
		global $path;		
		
		$pathTypes="/".$repository."/Types";
		$conditionTypes='TypeName="'.$node.'"';
		$attrsTypes=array($pathTypes.":Path");
		
		$cl->selectAttr($attrsTypes,$conditionTypes);
		if(!$cl->eot()){
			$path=$cl->getSelectAttrEntry();
		}
		//echo "repository";
		//echo "path:".$path[0]."\n";		
		//echo "hola";

}
/////////////////////////////////////////////////////////////////////////////
//////////////////////FUNCTION////////////////////////////////////////////////

			
	//error_log(print_r($_POST, true), 3, "postErr.log");
	//error_log(print_r($_GET, true), 3, "getErr.log");
	
	
	
	
?>

