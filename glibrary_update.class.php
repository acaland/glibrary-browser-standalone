<?php
	require_once "mdclient.php";
	require_once "config.php";
	require_once "lcg-infosites.class.php";
	class GLibrary// extends MDClient
{
	private $AMGA_HOST;// = 'glibrary.ct.infn.it';
	private $AMGA_PORT;// = 8822;
	private $ARRAY_FIELDS_HIDDEN;
	private $ARRAY_FIELDS_VISIBLE;
	private $ARRAY_FIELDS_RECORD;
	private $CERTIFICATES;	
	private $client;
	private $CONDITION;
	private $GENERIC_ATTR;
	private $FILTER;//filter values
	private $LOGIN;// = 'tcaland';
	private $NODE;	
	private $PATH;//="";	
	private $ENTRIES_DIR;
	private $CERT_KEY;// = '/tmp/x509up_u500';
	private $FILEID;
	
	private $RECORD;
	private $REPOSITORY;	
	
	
	/**
	* Constructor method
	* @access public
	* @param string $host AMGA server hostname
	* @param integer $port AMGA server port
	* @param string $login username used to access AMGA
	* @param string $proxy_file proxy path
	* @param string $repository current colecction.
	* @return void
	*/
	public function __construct($host, $port, $login, $certkey, $repository,$certificatePath)
	{
		$this->AMGA_HOST=$host;
		$this->AMGA_PORT=$port;
		$this->LOGIN=$login;
		$this->CERT_KEY=$certkey;
		$this->PATH="";
		$this->REPOSITORY=$repository;
		$this->CERTIFICATES=$certificatePath;
		$this->CONDITION="distinct";
		//parent::__construct($host, $port, $login, $password, $keepalive);
		//$this->collection = $collection;
	}
	/**
	* Conexion method
	* @access public	
	* @return boolean
	*/


	public function connectSSL(){
		try {
			$this->client = new MDClient($this->AMGA_HOST, $this->AMGA_PORT, 'root','gl1br@r1',true);
			// $this->client->requireSSL($this->CERT_KEY, $this->CERT_KEY, $this->CERTIFICATES);
			$this->client->connect();
			$this->client->sudo($this->LOGIN);
			return true;
		} catch (Exception $e) 	{
		echo 'Unable to connect to AMGA: ',  $e->getMessage(), "\n";
			return false;
		}		
		
	}
	
	/**
	* Get certificates path method
	* @access public
	* @return string
	*/
	public function getCertificates(){
		return $this->CERTIFICATES;
	}
	
	/**
	* Method:get generic attributs																																																																																																												 
	* @access public
	* @return array
	*/
	public function getGenericAttrs()
	{
		list($attrs, $types)=$this->client->listAttr("/".$this->REPOSITORY."/Entries");
		
		return $attrs;
	}
	
	/**
	* Method:return filters of a entry																																																																																																												 
	* @access public
	* @return array string
	*/
	
	public function getFilters($location,$Name){		
		
		$path="/".$this->REPOSITORY."/".$location;		
		$attrs=array($path.":FilterAttrs","FilterLabels");
		$condition=$Name.'="'.$this->NODE.'" distinct ';
		$result= $this->client->selectAttr($attrs,$condition);
		
		
		if (!$this->client->eot()) {
			$filter = $this->client->getSelectAttrEntry();
			$titlesColums=$this->client->getSelectAttrEntry();
		}
		
		if($filter[0]==""){
			   $data["filter"]="--";
			   $arr[]=$data;
		}else{
			///////////////GET NOME's Filters
			if ($titlesColums[0]) 
				$tokTitle = explode(",", $titlesColums[0]);
			
			/////////GET FILTERS
		   	$tok = strtok ($filter[0]," ");
			$i=0;
			while ($tok !== false) {
			  
			   $data["filter"]=$tok;
			   ////set title filter, it's displayed
			  if ($titlesColums[0]) {
			  		
				   $data["title"]=$tokTitle[$i];//title
				   
			   }else
			   	$data["title"]=$tok;
				
			   $tok = strtok(" \n\t");
			  $arr[]=$data;
			  $i++;
			}
		}
		  
		
		return $arr;
		
	} 
	
	/**
	* Method:get array values, to filtering																																																																																																										 
	* @access public
	* @return array fields
	*/
	public function getFilterArr($attrName, $isCollection){	
	
		//error_log(print_r('--> in getFilterArr ==$this->PATH : '.$this->PATH."\n", true), 3, "/tmp/postErr.log");
		//$entriesPath="/".$this->REPOSITORY."/Entries";
		$query = 'SELECT MAX(ARRAY_UPPER('.$attrName.',1)) FROM ';
		if ($isCollection) {
			//$join = " JOIN /$this->REPOSITORY/Collections ON /$this->REPOSITORY/Entries.FILE=/$this->REPOSITORY/Collections.EntryID";
			//$where = "/$this->REPOSITORY/Collections.CollID=$this->FILEID";
			$join = " JOIN $this->PATH ON $this->ENTRIES_DIR.FILE=$this->PATH.FILE";
			
			//$query = $query.$join." WHERE ".$where;
			$query = $query . $this->ENTRIES_DIR. $join;
			$query2_path = $this->ENTRIES_DIR;
		} else {
			$query = $query . $this->PATH;
			$query2_path = $this->PATH;
			$join = "";
		}
		list($operation,$max)=$this->client->getSQLEntry($query);
		//echo $tmp[0]."000000";
		$query2 = 'SELECT DISTINCT '.$attrName.'[idx] FROM '.$query2_path.$join.', GENERATE_SERIES(1,'.$max[0].') idx';
		$where2 = 'WHERE idx <= ARRAY_UPPER('.$attrName.',1)';
		//if ($isCollection) 
		//	$where2 = $where2." AND ".$where;
		$query2 = $query2." ".$where2;
		list($file,$tmp)=$this->client->getSQLEntry($query2);	
		for ($a = 0,$b = sizeof($tmp);$a < $b;$a++){
			$data["attr"]=$tmp[$a];
			$values[]=$data;
		}	
		if($a==0){
			$data["attr"]="-Not Available-";
			$values[]=$data;
		}
		return ($values);
				
	}
	/**
	* Method:get Fields, only visible 'Visible_attrs'																																																																																																										 
	* @access public
	* @return array fields
	*/
	function getHiddenFields(){
		return $this->ARRAY_FIELDS_HIDDEN;
	}
	/**
	* Method:get Fields, record field																																																																																																										 
	* @access public
	* @return array fields
	*/
	function getRecordFields(){
		return $this->ARRAY_FIELDS_RECORD;
	}
	/**
	* Get node method
	* @access public
	* @return string
	*/
	public function getNode(){
		return $this->NODE;
	}
	
	
	/**
	* Get User method
	* @access public
	* @return string
	*/
	public function getProxyUser(){
		$data["name"]=$this->client->whoami();				
		return $data;
	}
	
	/**
	* Get repository method
	* @access public	
	* @return string
	*/
	public function getRepository(){
		return  $this->REPOSITORY;
	}
	
	
	/**
	* Method:get Field entry. Only record select																																																																																																												 
	* @access public
	* @param string $ident, ID entry
	* @return array strings
	*/
	function getSelectRecord($ident){
		//$this->selectFieldsbyRecord();
		if(sizeof($this->ARRAY_FIELDS_RECORD)==0){
			$data["name"]="-No available-";
			$arr[]=$data;
			return $arr;
		}
			
			$attrs[0]=$this->PATH.":".$this->ARRAY_FIELDS_RECORD[0]["name"];
			
			for ($a = 1,$b = sizeof($this->ARRAY_FIELDS_RECORD);$a < $b;$a++){				
				$attrs[]=$this->ARRAY_FIELDS_RECORD[$a]["name"];
				//echo $attrs[$a];
			}
			
		$condition='FILE="'.$ident.'" distinct ';
		
		$result = $this->client->selectAttr($attrs,$condition);
			
		if(!$this->client->eot()){
			
		  while (!$this->client->eot()) {
			for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_RECORD);$a < $b;$a++){
				$info=$this->client->getSelectAttrEntry();
				
			/*	if(($this->ARRAY_FIELDS_RECORD[$a]["name"]=="Thumb")&&($info[0]!="")){					
					$data["Thumb"]="https://glibrary.ct.infn.it/glibrary_new/glibrary_conexion_update.php?task=GETTHUMB&rep=".$this->REPOSITORY."&idImg=".$info[0];
					
				}else{			*/
				  $info=$this->parseTypeForEdit($a,$info[0],$this->ARRAY_FIELDS_RECORD);				  
				  
				  $data[$this->ARRAY_FIELDS_RECORD[$a]["name"]]=$info; 
				 //} 
			}
			
			$arr[]=$data;	
		  }
		}else{
			$data["name"]="-No available-";
			$arr[]=$data;
		}
		//$data[""]="-No available-";
		//$arr[]=$data;
		return $arr;
	}

	/**
	* Get Storage Elements
	* @access public
	* @param void
	* @return array
	*/
	public function getStorageElements(){
		$is = new LCGInfosites();
		if($is->connect(BDII_HOST, BDII_PORT, VO)==false)
			return false;
			
		$se_list = $is->listSE();
		$num_results = $se_list["count"];
		
		for ($i = 0; $i < $num_results; $i++) 
		{
			
			$path=$se_list[$i]["gluesapath"][0];
			$dn=$se_list[$i]["dn"];
			$hostname = $is->getAttributeFromDN($dn, "GlueSEUniqueID");			
			$data["hostname"]=$hostname;
			$data["path"]=$path;
			$arr[]=$data;
			
			$data = null;
			
		}
		
		return $arr;
	}
	/**
	* Method:convert binary image to image .jpg																																																																																																												 
	* @access public
	* @return void
	*/
	public function getThumbnail($id) {
	
		
		//$this->client->getattr($pathImg, array("Thumb"));
		$path = "/".$this->REPOSITORY."/Thumbs/".$id;
		$this->client->getattr($path, array("Data"));
		if (!$this->client->eot()) {
			$row = $this->client->getEntry();
			$thumbdata=$row[1][0];
		}
		if (strncmp("http://", $thumbdata, 7)==0 || strncmp("https://", $thumbdata, 8)==0)
			header("Location:".$thumbdata);
		else {
			$im = imagecreatefromstring((base64_decode($thumbdata)));
		
			header("Content-type: image/jpg");
			imagejpeg($im);
			imagedestroy($im);
		}

	}
	
	
	/**
	* Method:get tree nodes of colecction by Types																																																																																																												 
	* @access public
	* @return void
	*/
	function getTreeTypes() {		
		$path = "/".$this->REPOSITORY."/Types";
		$this->client->getattr($path, array("TypeName","ParentID"));
		if($this->client->eot()){
			$data["name"]="--";
			$data["id"]="0";
			$data["parentID"]="0";
			$data["isFolder"]="false";
			$arr[]=$data;
		}
		while (!$this->client->eot()) {
			$row = $this->client->getEntry();
			$data["name"]=$row[1][0];
			$data["id"]=$row[0];
			$data["parentID"]=$row[1][1];
			$data["isFolder"]="false";
			//$data["openProperty"]="isOpen";
			$arr[]=$data;
		}		
		return $arr;		
	}
	/**
	* Method:get tree nodes of colecction by Collection																																																																																																												 
	* @access public
	* @return void
	*/
	function getTreeCollection() {		
		$path = "/".$this->REPOSITORY."/CollectionTree";
		//selectattr /EELA/Entries:FileName Description
		$this->client->getattr($path, array("Name","ParentID"));
		if($this->client->eot()){
			$data["name"]="--";
			$data["id"]="1";
			$data["parentID"]="0";
			$data["isFolder"]="false";
			$arr[]=$data;
		}
		while (!$this->client->eot()){
			$row = $this->client->getEntry();
			$data["name"]=$row[1][0];
			$data["id"]=$row[0];
			$data["parentID"]=$row[1][1];
			$data["isFolder"]="false";
			
			$arr[]=$data;
		}		
		return $arr;		
	}
	/**
	* Method:get attributs of a filter.																																																																																																												 
	* @access public
	* @return void
	*/
	
	function getValuesFilter($typeFilter,$isCollection){		
		
  	  	
	   $attrs=array($typeFilter);
	   $isArray=$this->IsArray($typeFilter,$this->ARRAY_FIELDS_HIDDEN);		
	   //error_log(print_r("is array: $isArray\n", true), 3, "/tmp/postErr.log");
	   //error_log(print_r($this->ARRAY_FIELDS_HIDDEN, true), 3, "/tmp/postErr.log");
	   if($isArray)//Take array values.	  
		return($this->getFilterArr($typeFilter,$isCollection));
	   	
	   //error_log(print_r($attrs, true), 3, "/tmp/postErr.log");
	   if ($isCollection) {
	   		error_log(print_r('--> in getValuesFilter ==$this->PATH : '.$this->PATH."\n", true), 3, "/tmp/postErr.log");
	   		error_log(print_r('--> in getValuesFilter ==$typeFilter : '.$typeFilter."\n", true), 3, "/tmp/postErr.log");
	   		//$entriesPath="/".$this->REPOSITORY."/Entries";
	   		$attrs=array($this->ENTRIES_DIR.".$typeFilter");
	   		
	   		//$this->PATH = "$this->PATH JOIN /$this->REPOSITORY/Collections ON /$this->REPOSITORY/Entries.FILE=/$this->REPOSITORY/Collections.EntryID ";
	   		$this->PATH = "$this->ENTRIES_DIR JOIN $this->PATH ON $this->ENTRIES_DIR.FILE=$this->PATH.FILE ";
	   		//$where = "/$this->REPOSITORY/Collections.CollID=$this->FILEID";
	   		
	   		//error_log(print_r('--> in getValuesFilter ==>this->CONDITION : '.$this->CONDITION."\n", true), 3, "/tmp/postErr.log");	   		
	   		/*if ($this->CONDITION == " ")
	   			$this->CONDITION = " WHERE $where";
	   		else
	   			$this->CONDITION = "$this->CONDITION AND $where"; */
	   }
		$this->client->selectAttrSQL($attrs, $this->CONDITION,$this->PATH);		
		
		//error_log(print_r('$this->PATH : '.$this->PATH."\n", true), 3, "/tmp/postErr.log");
		//error_log(print_r('$this->CONDITION :'.$this->CONDITION."\n", true), 3, "/tmp/postErr.log");
		
		//if(!$this->client->eot()){
		   $rabbish_path=$this->client->getSelectEntrySQL();
		   $data["attr"]="ALL";
		   $arr[]=$data;
		   if(!$this->client->eot()){
		     while (!$this->client->eot()) {			 
			 $valueAtr= $this->client->getSelectEntrySQL();
			 if($valueAtr[0]!=""){
				$data["attr"]=$valueAtr[0];				
			 	$arr[]=$data;			 
				$data=null;
			 }
		     }
		   }
		
		  return $arr;
		
	
	}
	
	
	/**
	* Method:get generic attributs																																																																																																												 
	* @access private
	* @return array
	*/
	private function getVisibleAttrs($visualPath)
	{
		$this->GENERIC_ATTR=$this->getGenericAttrs();		
		$isVisibleFILE=false;		
		$arrayVisible= array();
		
		$file="/".$this->REPOSITORY.$visualPath."/".$this->FILEID; 
		$attrs=array("VisibleAttrs","ColumnLabels","ColumnWidth","ThumbHeight","ThumbWidth");
		$result= $this->client->getattr($file,$attrs);
		
		$rabbish=$this->client->getSelectAttrEntry();
		///////////////////VISIBLE ATTRIBUTS/////////////
		$filter = $this->client->getSelectAttrEntry();						
		$tokvisibleAt = explode(" ", $filter[0]);
			
		///////////////////EXTRACT COLUMLABELS///////////
		$titlesColums=$this->client->getSelectAttrEntry();	
		if ($titlesColums[0]) 
		//	error_log(print_r($titlesColums, true), 3, "/tmp/postErr.log");
			$toktitle = explode(",", $titlesColums[0]);
			
		///////////////////EXTRACT COLUMWIDTH////////////
		$widthColums=$this->client->getSelectAttrEntry();									
		$tokWidth = explode(" ", $widthColums[0]);			
		
		$thumbHeight = $this->client->getSelectAttrEntry();
		$thumbWidth = $this->client->getSelectAttrEntry();
		
		//$format = $this->client->getSelectAttrEntry();
		//$tokFormat = explode("|", $format[0]);
		//error_log(print_r($tokFormat, true), 3, "/tmp/postErr.log");
			
		///////////////////SET ATTRIBUTOS TITLE, WIDTH, GENERIC, NAME////////////////
		$contTitle=0;
		$contWidth=0;
		for ($a = 0,$b = sizeof($tokvisibleAt);$a < $b;$a++){
			if($tokvisibleAt[$a]!=""){
				$data["name"]=$tokvisibleAt[$a];      
				if($tokvisibleAt[$a]=="Thumb"){			
					$data["type"]="image";
					$data["cellAlign"]="center";
					//$data["imageType"]="center";
					//$data["width"]=180;
					$data["imageSize"]=60;
					//error_log(print_r($thumbHeight, true), 3, "/tmp/postErr.log");
					
					if ($thumbHeight[0]!="") {
						$data["imageHeight"]=(int)$thumbHeight[0];
						//error_log(print_r("ci vado". $thumbHeight[0], true), 3, "/tmp/postErr.log");
					}
					if ($thumbWidth[0]!="")
						$data["imageWidth"]=(int)$thumbWidth[0]; 
				}
			
				$data["showIf"]="true";
				
				//if ($tokFormat[$a] != "")
				//	$data["format"]="eval($tokFormat[$a])";
				
				
				if($titlesColums[0]) {
					$data["title"]=$toktitle[$a];					
				}
				
				if($tokWidth[$a] !=""){
					$data["width"]=$tokWidth[$a];					
				}else{
					$data["width"]=150;
				}
				
				if(in_array($tokvisibleAt[$a],$this->GENERIC_ATTR)==false){
					$data["generic"]="false";
				}else $data["generic"]="true";
				
				if($tokvisibleAt[$a]=="FILE"){
					$isVisibleFILE=true;
					$data["primaryKey"]="true";
				}
			
				$arrayVisible[]=$tokvisibleAt[$a];
				$this->ARRAY_FIELDS_VISIBLE[]=$data;
				$this->ARRAY_FIELDS_HIDDEN[]=$data;
				$data=null;
		   		
			}
		}
			
			
		
		////ADD ATTRIBUT FILE////
		if($isVisibleFILE==false){
				
			$dataKey["name"]="FILE";				
			$dataKey["showIf"]="true";
			$dataKey["primaryKey"]="true";	
			$dataKey["width"]=50;
			$arrayVisible[]="FILE";
			$this->ARRAY_FIELDS_VISIBLE[]=$dataKey;
			$this->ARRAY_FIELDS_HIDDEN[]=$dataKey;
		}	
		return $arrayVisible;
	}
	
	
	/**
	* Method:get Fiels, all attributs visible																																																																																																												 
	* @access public
	* @return array fields
	*/
	function getVisibleFields(){
		return $this->ARRAY_FIELDS_VISIBLE;
	}
	
	/**
	* Method: test type of attributes, true if is array. 																																																																																																												 
	* @access private
	* @return boolean
	*/
	public function IsArray($nameFilter,$arrayFields){		
		
		$a=0;
		$find=false;
		while ($find==false){
			$name=$arrayFields[$a]["name"];
			
			if($name==$nameFilter){
				$type=$arrayFields[$a]["type"];
				$typeArray=$arrayFields[$a]["typeShow"];
				//echo $type;
				if(($type=="varchar[]")||($typeArray=="varchar[]")){					
					//echo "hola".$arrayFields[$a]["typeShow"]." tipo ".$type." name: ".$arrayFields[$a]["name"];
					return true;
				}else 
					return false;
			}else {
				if($name==""){
					
					return false;
				}
			}
			$a++;		
		}				
	}
	/**
	* Method:get Fields entries. List all record follow conditions.																																																																																																												 
	* @access public
	* @return array all rows
	*/
	function listAllSelectRecords(){	
		
		if(sizeof($this->ARRAY_FIELDS_HIDDEN)==0){
			$data["name"]="-No available-";
			$arr[]=$data;
			return $arr;
		}
			
			for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_HIDDEN);$a < $b;$a++){
				
				$attrs[]=$this->ARRAY_FIELDS_HIDDEN[$a]["name"];
				//echo $attrs[$a];
				
			}	
		//error_log(print_r('$this->PATH : '.$this->PATH."\n", true), 3, "/tmp/postErr.log");
		$this->client->selectAttrSQL($attrs, $this->CONDITION,$this->PATH);	
		
		if(!$this->client->eot()){
			$rabbish_path=$this->client->getSelectEntrySQL();
			
		  while (!$this->client->eot()) {			  
			  $IsThumb=false;
			  $array_returned=$this->client->getSelectEntrySQL();
			  
			  
			  
			  
			  //error_log(print_r($array_returned, true), 3, "/tmp/postErr.log");
			  //error_log(print_r("\n\n"), 3, "/tmp/postErr.log");
			for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_HIDDEN);$a < $b;$a++){			
				
				if(($this->ARRAY_FIELDS_HIDDEN[$a]["name"]=="Thumb")&&($array_returned[$a]!="")){				
						$data["Thumb"]="http://glibrary.ct.infn.it/glibrary_new/glibrary_conexion_update.php?task=GETTHUMB&rep=".$this->REPOSITORY."&idImg=".$array_returned[$a];
							
				}else{
				  $info=$this->parseType($a,$array_returned[$a],$this->ARRAY_FIELDS_HIDDEN);
				  
				  
				  
				  //error_log(print_r($info, true), 3, "/tmp/postErr.log");
				  //echo $this->ARRAY_FIELDS_HIDDEN[$a]["name"]."==".$info."*******";
				  $data[$this->ARRAY_FIELDS_HIDDEN[$a]["name"]]=$info;				
				}
			}
			
			$arr[]=$data;
			$data=null;
		  }	  
		  //select return -> nothing
		}else{
			$data["name"]="-No available-";
			$arr[]=$data;
			
		}
		/*ob_start();
		var_dump($arr);
		$logf = ob_get_contents();
		ob_end_clean();
		error_log($logf, 3, "/tmp/postErr.log"); */
		
		return $arr;
	
	}
	/**
	* Get entries by collection, with a collection ID(FILEID)
	* @access public
	* @param void
	* @return array entries
	*/
	
	public function listCollection(){
		if(sizeof($this->ARRAY_FIELDS_HIDDEN)==0){
			$data["name"]="-No available-";
			$arr[]=$data;
			return $arr;
		}
			//echo $this->PATH;
			
			
		//$entriesPath="/".$this->REPOSITORY."/Entries";
		$collectionPath="/".$this->REPOSITORY."/Collections";
			
		//error_log(print_r('$this->PATH : '.$this->PATH."\n", true), 3, "/tmp/postErr.log");
		for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_HIDDEN);$a < $b;$a++){
				
				//$attrs[]=$this->PATH.".".$this->ARRAY_FIELDS_HIDDEN[$a]["name"];
				$attrs[]=$this->ENTRIES_DIR.".".$this->ARRAY_FIELDS_HIDDEN[$a]["name"];
				
		}	
		error_log(print_r('$this->CONDITION : '.$this->CONDITION."\n", true), 3, "/tmp/postErr.log");
		
		if($this->CONDITION==" ")
			$condition=" WHERE ";
		else
			$condition=$this->CONDITION." AND ";
		//$condition=$condition."/EELA/Entries.FILE = /EELA/Collections.EntryID AND /EELA/Collections.CollID = ".$this->FILEID;
		//if ($entriesPath == $this->PATH) 
		//	$condition=$condition."/".$this->REPOSITORY."/Entries.FILE = /".$this->REPOSITORY."/Collections.EntryID AND /".$this->REPOSITORY."/Collections.CollID = ".$this->FILEID;
		//else {
			$condition=$condition."$this->ENTRIES_DIR.FILE = ".$this->PATH.".FILE";
			$collectionPath = $this->PATH;
		//}
		//error_log(print_r('$this->PATH : '.$this->PATH."\n", true), 3, "/tmp/postErr.log");
		//error_log(print_r('condition: '.$condition."\n", true), 3, "/tmp/postErr.log");
		
		$this->client->selectAttrSQL($attrs, $condition, $this->ENTRIES_DIR.", ". $collectionPath);	
		
		if(!$this->client->eot()){
			
			//for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_HIDDEN);$a < $b;$a++){
				$rabbish_path=$this->client->getSelectEntrySQL();	
			//}
		  while (!$this->client->eot()) {			  
			  $IsThumb=false;
			  $array_returned=$this->client->getSelectEntrySQL();
			for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_HIDDEN);$a < $b;$a++){			
				
				if(($this->ARRAY_FIELDS_HIDDEN[$a]["name"]=="Thumb")&&($array_returned[$a]!="")){				
						
						$data["Thumb"]="http://glibrary.ct.infn.it/glibrary_new/glibrary_conexion_update.php?task=GETTHUMB&rep=".$this->REPOSITORY."&idImg=".$array_returned[$a]; //.$title[0];
									
				}else{
				  $info=$this->parseType($a,$array_returned[$a],$this->ARRAY_FIELDS_HIDDEN);
				  $data[$this->ARRAY_FIELDS_HIDDEN[$a]["name"]]=$info;				
				}
			}
			
			$arr[]=$data;
			$data=null;
		  }	  
		  //select return -> nothing
		}else{
			$data["name"]="-No available-";
			$arr[]=$data;
			
		}
		
		return $arr;
		
	}
	
	/**
	* Method: getEntryFilename
	* @access public
	* @param string $ident, ID entry
	* @return string
	*/
	function getEntryFilename($ident){
		$entry = "/".$this->REPOSITORY."/Entries/".$ident;
		$this->client->getattr($entry, array("FileName"));
		if (!$this->client->eot()) {
			$row = $this->client->getEntry();
			$filename=$row[1][0];
		}
		return $filename;
	}
	
	
	/**
	* Method:get Surls values of a entry.																																																																																																												 
	* @access public
	* @param string $ident, ID entry.
	* @return array strings
	*/
	function listSurls($ident){		
		$pathSURL="/".$this->REPOSITORY."/Replicas";
		$conditionSURL='ID="'.$ident.'"';
		$attrsSURL=array($pathSURL.":surl");
		
		$this->client->selectAttr($attrsSURL,$conditionSURL);
		if(!$this->client->eot()){
			while (!$this->client->eot())
			{
				$tmp= $this->client->getSelectAttrEntry();
			
				$tmp[0] = str_replace( "srm://", "https://", $tmp[0]);
				$tmp[0] = str_replace( "gsiftp://", "https://", $tmp[0]);			
				$data["name"]=$tmp[0];
				$data["linkText"]=$data["name"];
				$data["type"]="link";
				$surl[] = $data;
				
			}
		}else{
			$data["name"]="-No available-";			
			$surl[]=$data;
		}
		
		return $surl;
		
	}
	
	/**
	* Method:get entries relationed with a entry.																																																																																																												 
	* @access public
	* @param string $ident, ID entry.
	* @return array strings
	*/
	//selectattr /EELA/Entries:FileName Description '/EELA/Entries:FILE=/EELA/Relations:ID and ID=150'
	function listRelations($ident){		
		$path="/".$this->REPOSITORY."/Entries";
		$pathRelations="/".$this->REPOSITORY."/Relations";
		$condition=$path.":FILE=".$pathRelations.":RelID and ID=".$ident;
		$attrs=array($path.":FileName",$path.":Description",$path.":FILE");
		
		$this->client->selectAttr($attrs,$condition);
		if(!$this->client->eot()){
			while (!$this->client->eot())
			{
				$tmp= $this->client->getSelectAttrEntry();			
				$data["FileName"]=$tmp[0];
				$tmp= $this->client->getSelectAttrEntry();
				$data["Description"]=$tmp[0];
				$tmp= $this->client->getSelectAttrEntry();
				$data["FILE"]=$tmp[0];
				$arr[] = $data;
				
			}
		}else{
			$data["name"]="-No available-";			
			$arr[]=$data;
		}
		
		return $arr;
		
	}
	
	/**
	* Parse data type varchar [], convert format to correcto text format
	* @access private
	* @param int attribut position in ARRAY_FIELD, string with data varchar[]
	* @return void
	*/
	private function parseType($position,$title,$arrayFields){		
		$type=$arrayFields[$position]["type"];
		//error_log(print_r($type."\n", true), 3, "/tmp/postErr.log");
		if($type=="varchar[]"){			
			$title1=str_replace(', ','##',$title);
			$tokArray = split('","|",|,"|,|{"*|"*}', $title1);//convert string to array,clean characters "{ and }"
			$data="";
			$long=sizeof($tokArray);
			for ($a = 1,$b = $long-1;$a < $b;$a++){
				$tmp=str_replace('##',', ',$tokArray[$a]);
				$data=$data.$tmp." <br> ";	//make apropiate string, separator <br>			
			}
			
			return $data;
		
		}else if ($type=="integer")
			return intval($title);
		else
			return $title;
	
	}
	/**
	* Parse data type varchar [], convert format to correcto text format
	* @access private
	* @param int attribut position in ARRAY_FIELD, string with data varchar[]
	* @return void
	*/
	private function parseTypeForEdit($position,$title,$arrayFields){		
		$type=$arrayFields[$position]["typeShow"];
		if($type=="varchar[]"){		
			//error_log(print_r('$title :'.$title, true), 3, "/tmp/postErr.log");
			$title1=str_replace(', ','##',$title);
			//error_log(print_r('$title1 :'.$title1, true), 3, "/tmp/postErr.log");
			$tokArray = split('",|,"|","|,|{"*|"*}', $title1);//convert string to array,clean characters "{ and }"
			//error_log(print_r('$tokArray :', true), 3, "/tmp/postErr.log");
			//error_log(print_r($tokArray, true), 3, "/tmp/postErr.log");
			$data="";
			//echo $title;
			$long=sizeof($tokArray);
			for ($a = 1,$b = $long-2;$a < $b;$a++){
				$tmp=str_replace('##',', ',$tokArray[$a]);
				//error_log(print_r('$tmp :'.$tmp, true), 3, "/tmp/postErr.log");
				$data=$data.$tmp."\n";	//make apropiate string, separator <br>			
			}
			$tmp=str_replace('##',', ',$tokArray[$b]);
			$data=$data.$tmp;
			//error_log(print_r('$data :'.$data, true), 3, "/tmp/postErr.log");
			return $data;
		
		}else		
			return $title;
	
	}
	
	
	/**
	* Get Collection path method
	* @access public
	* @return boolean, true if successfully
	*/
	
	public function putPathCollection(){		
		
		$this->PATH="/".$this->REPOSITORY."/Entries";
		
		$path="/".$this->REPOSITORY."/CollectionTree";
		$condition='Name="'.$this->NODE.'"';
		$attrs=array($path.":FILE","Path","EntriesDir");
		$this->client->selectAttr($attrs,$condition);		
		if(!$this->client->eot()){							
			$tmp=$this->client->getSelectAttrEntry();
			$this->FILEID=$tmp[0];
			$tmp=$this->client->getSelectAttrEntry();
			$this->PATH = $tmp[0];
			$tmp=$this->client->getSelectAttrEntry();
			$this->ENTRIES_DIR = $tmp[0];
							
			return $this->PATH;
		}else{
			return false;
		}
	}
	
	/**
	* Get Types path method, by record. DetailViewer
	* @access public
	* @param ident, identificate record
	* @return string
	*/
	public function putPathRecord($ident){
		$attrs=array("Path");
	 	$pathEntries=$this->REPOSITORY."/Entries";
	 	$pathTypes=$this->REPOSITORY."/Types";
	 	$condition=" WHERE ".$pathEntries.".TypeID=".$pathTypes.".FILE AND ".$pathEntries.".FILE=".$ident;
	 
	 	$this->client->selectAttrSQL($attrs, $condition,$pathEntries.", ".$pathTypes);		
		if(!$this->client->eot()){
			$rabbish_path=$this->client->getSelectEntrySQL();
			$value= $this->client->getSelectEntrySQL();
			$this->PATH=$value[0];		  
		}
		return $this->PATH;
	}
	
	/**
	* Get Types path method, by types
	* @access public
	* @return * @return boolean, true if successfully
	*/		
	public function putPathTypes(){	
		$path="/".$this->REPOSITORY."/Types";
		$condition='TypeName="'.$this->NODE.'"';
		$attrs=array($path.":Path","FILE");		
		
		$this->client->selectAttr($attrs,$condition);
		if(!$this->client->eot()){
			$tmp=$this->client->getSelectAttrEntry();
			$this->PATH=$tmp[0];			
			$tmp=$this->client->getSelectAttrEntry();
			$this->FILEID=$tmp[0];
			//echo '{\'path\': \'' . $this->PATH. '\'}';
			//echo "{path:".$this->PATH."}";
			error_log(print_r($this->PATH, true), 3, "/tmp/postErr.log");
			return $this->PATH;
		}else{
			return false;
		}
		
	}
	/**
	* Method:get Fields entrys. Listgrid y collections.																																																																																																												 
	* @access public
	* @return boolean
	*/
	function selectFieldsbyCollections(){
		
		$arrayVisible= array();
		$attrGeneric= array();
		$arrayVisible=$this->getVisibleAttrs("/CollectionTree");
		$attrGeneric=$this->GENERIC_ATTR;
			
		///////////////////////COMPARE GENERIC ATTRS//////////////////////////////////////		
			//list($attrs,$types)=$this->client->listAttr("/".$this->REPOSITORY."/Entries");
			list($attrs,$types)=$this->client->listAttr($this->ENTRIES_DIR);
			$attrs = array_merge($attrs, array("OWNER","PERMISSIONS", "GROUP_RIGHTS"));
			
			//error_log(print_r($attrs, true), 3, "/tmp/postErr.log");
			
			for ($a = 0,$b = sizeof($attrs);$a < $b;$a++){
				$position=array_search($attrs[$a],$arrayVisible);//get attribute position or false if it's not visible
				
				if($position===false){
					
					//hidden attributs
					 $data["name"]=$attrs[$a];
					 $data["width"]=90;
					 $data["type"]=$types[$a];
					 if($attrs[$a]=="Thumb"){			
						 $data["type"]="image";
						 $data["imageHeight"]=60;
						 $data["imageWidth"]=50;
					 }
					
						 //$data["type"]=$types[$a];
					 $data["showIf"]="false";				
					
					 ////all visible attributs					
					 $this->ARRAY_FIELDS_HIDDEN[]=$data;					
					 $data["showIf"]="true";
					 $this->ARRAY_FIELDS_VISIBLE[]=$data;
					 $data=null;
					 
				}else {//put type to visible attributes
					if($attrs[$a]!="Thumb"){
						$this->ARRAY_FIELDS_HIDDEN[$position]["type"]=$types[$a];
						$this->ARRAY_FIELDS_VISIBLE[$position]["type"]=$types[$a];
					}
				}
			}
			
			
		return true;
	}
	
	/**
	* Method:get Fields entrys. Detail Viewer.																																																																																																												 
	* @access public
	* @return boolean
	*/
	function selectFieldsbyRecord(){		
		
		$attrGeneric= array();
		$this->GENERIC_ATTR=$this->getGenericAttrs();
		error_log(print_r("\npath :" . $this->PATH . "\n", true), 3, "/tmp/postErr.log");
		
		///////////////////////LISTATTRS//////////////////////////////////////		
		list($attrs,$types)=$this->client->listAttr($this->PATH);
		
		error_log(print_r($attrs, true), 3, "/tmp/postErr.log");
		
			$attrs = array_merge($attrs, array("OWNER","PERMISSIONS", "GROUP_RIGHTS"));
			$dataKey["name"]="FILE";				
			$dataKey["showIf"]="true";
			//$dataKey["primaryKey"]="true";	
			$dataKey["width"]=50;
			$this->ARRAY_FIELDS_RECORD[]=$dataKey;
			for ($a = 0,$b = sizeof($attrs);$a < $b;$a++){				
				 $arrProperties=$this->setFieldsProperties($types[$a]);
				 
				 $data["name"]=$attrs[$a];				 
				 $data["width"]=$arrProperties[0];								 
				 $data["type"]=$arrProperties[1];
				 $data["typeShow"]=$arrProperties[2];
				 $data["title"]="<b>".$attrs[$a]."</b><br><i>".$arrProperties[2]."</i>";
				 /*if($attrs[$a]=="Thumb"){			
					 $data["type"]="image";
					 $data["imageHeight"]=120;
					 $data["imageWidth"]=100;
					 
					 
				 }*/
				 
				 $data["showIf"]="true";
				 if(in_array($attrs[$a],$this->GENERIC_ATTR)==false){
					 $data["generic"]="false";
				 }else $data["generic"]="true";
					
				 ////all visible attributs				 
				 $this->ARRAY_FIELDS_RECORD[]=$data;
				 $data=null;					 
		
			}
			
		return true;
	}
	/**
	* Method:get Fields entrys. Listgrid by types.																																																																																																												 
	* @access public
	* @return boolean
	*/
	function selectFieldsbyTypes(){
		
		$arrayVisible= array();
		$attrGeneric= array();
		$arrayVisible=$this->getVisibleAttrs("/Types");
		
		//$position=5;
	
		///////////////////////LISTATTRS//////////////////////////////////////		
			list($attrs,$types)=$this->client->listAttr($this->PATH);
			$attrs = array_merge($attrs, array("OWNER","PERMISSIONS", "GROUP_RIGHTS"));
			$types = array_merge($types, array("text","text","text"));
			for ($a = 0,$b = sizeof($attrs); $a < $b; $a++){
				$position=array_search($attrs[$a],$arrayVisible);//get attribute position or false if it's not visible
				switch ($types[$a]) {
						case "int": 
							$data["type"]="integer";
							break;
						case "timestamp": 
							$data["type"]="date";
							break;	
						case "varchar": 
							$data["type"]="text";
							break;		
						default:	
							$data["type"]=$types[$a];
						
					}
				if($position===false){
					//hidden attributs
					 $data["name"]=$attrs[$a];
					// $data["width"]=$this->setFieldsWidth($types[$a]);
					$data["width"]=150;
					
					 
					 if($attrs[$a]=="Thumb"){			
						 $data["type"]="image";
						 $data["imageHeight"]=60;
						 $data["imageWidth"]=50;
						 
					 }
					
						 //$data["type"]=$types[$a];
					 $data["showIf"]="false";
					 if(in_array($attrs[$a],$this->GENERIC_ATTR)==false){
						 $data["generic"]="false";
					 }else $data["generic"]="true";
					
					 ////all visible attributs					
					 $this->ARRAY_FIELDS_HIDDEN[]=$data;					
					 $data["showIf"]="true";
					 $this->ARRAY_FIELDS_VISIBLE[]=$data;
					 $data=null;
					 
				}else {//put type to visible attributes
					if($attrs[$a]!="Thumb"){
						$this->ARRAY_FIELDS_HIDDEN[$position]["type"]=$data["type"]; //$types[$a];
						$this->ARRAY_FIELDS_VISIBLE[$position]["type"]=$data["type"]; //$types[$a];
					}
				}
			}
						
			
		return true;
	}
	
	
	
	
	/**
	* Set certificates path method
	* @access public
	* @param string $path new path
	* @return void
	*/
	public function setCertificates($certificates){
		$this->CERTIFICATES=$certificates;
	}
	
	/**
	* Method:set condition to filter values .																																																																																																												 
	* @access public
	* @return void
	*/
	function setCondition($record,$filter,$isCollection){
		$this->CONDITION=" ";
		$flagEmpty=true;
		for ($a = 0,$b = sizeof($filter);$a < $b;$a++){
			
			if(($filter[$a]!="--")&&($record[$a]!="--")&&($record[$a]!="-No available-")&&($record[$a]!="ALL")){
				$IsArray=$this->IsArray($filter[$a],$this->ARRAY_FIELDS_HIDDEN);
				
			   if ($isCollection) {
			   		if($IsArray)
				 		$condition="'".$record[$a]."' =ANY(".$this->ENTRIES_DIR.".".$filter[$a].")";  
			        else
				        $condition=" ".$this->ENTRIES_DIR.".".$filter[$a]."= '".$record[$a]."'"; 
			   } else {
			   	   if($IsArray)
				        $condition="'".$record[$a]."' =ANY(".$this->PATH.".".$filter[$a].")";  
			       else
				        $condition=" ".$this->PATH.".".$filter[$a]."= '".$record[$a]."'"; 
				 
			   }
			   
			   if($flagEmpty){
				$this->CONDITION=$this->CONDITION." WHERE ".$condition;
					$flagEmpty=false;
			   }else{
					
				   $this->CONDITION=$this->CONDITION." AND ".$condition;				
			   }				
				
			}
		}	
	}	
	
	
	/**
	* Method:set data attributs in specific entry.																																																																																																												 
	* @access public
	* @param string $file, FILE attribut value of the entry.
	* @param string $arrKeys, array with attributs names.
	* @param string $arrValues, array with values to save.
	* @return void
	*/
	
	function setDataEntry($file,$arrKeys,$arrValues){
	
		//make the path for the entries, and set values in AMGA
		$pathSave=$this->PATH."/".$file;	
		$this->client->setAttr($pathSave, $arrKeys, $arrValues);	
		
	
	}
	/**
	* Method:set data attributs in types.																																																																																																												 
	* @access public
	* @param string $arrKeys, array with attributs names.
	* @param string $arrValues, array with values to save.
	* @return void
	*/
	
	function setDataTypes($arrKeys,$arrValues){
		$pathSet="/".$this->REPOSITORY."/Types/".$this->FILEID;	
		$this->client->setAttr($pathSet,$arrKeys,$arrValues);
				
	}
	
	/**
	* Method:set data attributs in CollectionTree.																																																																																																												 
	* @access public
	* @param string $arrKeys, array with attributs names.
	* @param string $arrValues, array with values to save.
	* @return void
	*/
	
	function setDataCollections($arrKeys,$arrValues){
		$pathSet="/".$this->REPOSITORY."/CollectionTree/".$this->FILEID;	
		$this->client->setAttr($pathSet,$arrKeys,$arrValues);	
		
		
	}
	
	/**
	* Method:set new entry  on de repository																																																																																																												 
	* @access public
	* @param array string $arrKeys
	* @param array string $arrValues, array with values to save.
	* @param array string $replicas, array with surl from replicas
	* @return void
	*/
	public function setEntry($arrKeys,$arrValues,$replicas,$collections){
		//create entry		
		
		$pathSeq="/".$this->REPOSITORY."/Entries/id";			
		$seq_id = $this->client->sequenceNext($pathSeq);		
		$this->client->addentries(array($this->PATH."/".$seq_id));
		
		//Set Replicas
		$attSURL = array("surl", "ID");
		for ($i = 0; $i < sizeof($replicas); $i++)
		{
			// get next replica id
			$rep_seq=$this->client->sequenceNext("/".$this->REPOSITORY."/Replicas/rep");
			echo $rep_seq;
			$rep_id = "r".$rep_seq;
			$values = array($replicas[$i], $seq_id);
			$this->client->addentry("/".$this->REPOSITORY."/Replicas/".$rep_id, $attSURL, $values);
		}
		//Set Collections
		if($collections[0]!=""){
			$this->setEntryCollection($seq_id,$collections);
			
		}
			
		//set attributes Values
		echo "uno->".json_encode($arrKeys)."q?".json_encode($arrValues);
		$attrs=array($this->PATH.":TypeID");
		$this->client->selectAttr($attrs,'distinct');		
		if (!$this->client->eot()) {
			$type = $this->client->getSelectAttrEntry();
			//$rabbish = $this->client->getSelectAttrEntry();			
			$arrKeys = array_merge($arrKeys,array("TypeID"));
			$arrValues = array_merge($arrValues, $type);			
		}
		//set submisionDate
		$arrKeys = array_merge($arrKeys,array("SubmissionDate"));
		$arrValues = array_merge($arrValues, array(date("Y-m-d H:i")));
		
		$this->client->setAttr($this->PATH."/".$seq_id, $arrKeys, $arrValues);
		
		//$collections[0]."y la dos".$collections[1];
		//echo "dos->".json_encode($arrKeys);
		//echo "tres->".json_encode($arrValues);
	}
	/**
	* Method:set new element on de collections																																																																																																												 
	* @access public
	* @param array string $id entry
	* @param array string $collections, array with names of collections.
	* @param array string $replicas, array with surl from replicas
	* @return void
	*/
	public function setEntryCollection($entryID,$collNames){
		$pathSeq="/".$this->REPOSITORY."/Collections/id";			
		$attCOLL = array("EntryID", "CollID");
		$attrs=array("/".$this->REPOSITORY."/CollectionTree:FILE");
		for ($a = 0,$b = sizeof($collNames);$a < $b;$a++){			
			$this->client->selectAttr($attrs,'Name="'.$collNames[$a].'"');		
			if (!$this->client->eot()) {				
				$collID=$this->client->getSelectAttrEntry();	
				$values = array($entryID, $collID[0]);
				$coll_seq = $this->client->sequenceNext($pathSeq);
				echo "collectionsec->".$coll_seq;
				$this->client->addentry("/".$this->REPOSITORY."/Collections/".$coll_seq, $attCOLL, $values);
				$values=null;
			}
		}
	
	}
	
	
	/**
	* Method: analize type, and if is array change the type for smartclient																																																																																																											 
	* @access public
	* @param string $type, is types' name	
	* @return array with types properties.
	*/
	function setFieldsProperties($type){		
		$plus=150;		
		
		$tok = split("varchar\(|\)",$type );		
		if($tok!=false)
			$plus=(int)$tok[1];
		
		$properties[0]=150+$plus;		
		$properties[1]=$type;
		$properties[2]=false;
		
		if(($type=="varchar[]")||($type=="varchar")){
			$properties[0]=200;
			$properties[1]="textArea";
		}
		
		$properties[2]=$type;
		
					
		return $properties;
	}
	
	/**
	* Method:set Fields, only visible 'Visible_attrs'																																																																																																										 
	* @access public
	* @return array fields
	*/	
	function setHiddenFields($arr){
		$this->ARRAY_FIELDS_HIDDEN=$arr;
	}
	
	
	/**
	* Set node method
	* @access public
	* @param string $node new node
	* @return void
	*/
	public function setNode($node){
		$this->NODE=$node;
	}

	/**
	* Method:set value to the attribut path.																																																																																																												 
	* @access public
	* @return array fields
	*/
	function setPath($path){
		$this->PATH=$path;	
	}
	
	
	
	/**
	* Set repository method
	* @access public
	* @param string $repository new repository
	* @return void
	*/
	public function setRepository($repository){
		$this->REPOSITORY=$repository;
	}
	

	
	
	/**
	* Method:set Fiels, all attributs visible																																																																																																												 
	* @access public
	* @return array fields
	*/
	function setVisibleFields($arr){
		$this->ARRAY_FIELDS_VISIBLE=$arr;	
	}
	
	public function prueba($ident){
		
		//$this->putPathRecord($ident);
		//echo $this->PATH;
		//$this->selectFieldsbyRecord();
		//$this->setCollectionID();
		//$this->selectFieldsbyTypes();
		//parseType($position,'{"Roberto Benigni","Nicoletta Braschi","Marisa Paredes","Giorgio Cantarini"}');
		//$this->CONDITION="/EELA/Entries:FILE=/EELA/Collections:EntryID and /EELA/Collections:CollID=2 and /EELA/Entries:TypeID=/EELA/Types:FILE"
		//$this->selectFieldsbyTypes();
		//echo json_encode($this->getVisibleFields());
		//echo"hola";
		//return($this->getVisibleAttrs());
		//$condition="/EELA/Entries:FILE=/EELA/Collections:EntryID and /EELA/Collections:CollID=2";
		//$this->getTreeCollection();
		//return($this->listCollection($condition));
		//echo json_encode($this->setFieldsProperties("varchar[]"));
		//echo "hola";
	}
	
	
}
?>
