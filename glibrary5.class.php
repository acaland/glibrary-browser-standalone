<?php
	require "mdclient.php";
	class GLibrary// extends MDClient
{
	private $AMGA_HOST;// = 'glibrary.ct.infn.it';
	private $AMGA_PORT;// = 8822;
	private $ARRAY_FIELDS_HIDDEN;
	private $ARRAY_FIELDS_VISIBLE;
	private $CERTIFICATES;	
	private $client;
	private $CONDITION;
	private $GENERIC_ATTR;
	private $FILTER;//filter values
	private $LOGIN;// = 'tcaland';
	private $NODE;	
	private $PATH;//="";	
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
			$this->client = new MDClient($this->AMGA_HOST, $this->AMGA_PORT, 'root');
			$this->client->requireSSL($this->CERT_KEY, $this->CERT_KEY, $this->CERTIFICATES);
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
			$tokTitle = explode(",", $titlesColums[0]);
			
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
		  
		
		return $arr;
		
	} 
	
	/**
	* Method:get array values, to filtering																																																																																																										 
	* @access public
	* @return array fields
	*/
	public function getFilterArr($attrName){	
		list($operation,$max)=$this->client->getSQLEntry('SELECT MAX(ARRAY_UPPER('.$attrName.',1)) FROM '.$this->PATH);
		//echo $tmp[0]."000000";
		list($file,$tmp)=$this->client->getSQLEntry('SELECT DISTINCT '.$attrName.'[idx] FROM '.$this->PATH.', GENERATE_SERIES(1,'.$max[0].') idx WHERE idx <= ARRAY_UPPER('.$attrName.',1)');	
		for ($a = 0,$b = sizeof($tmp);$a < $b;$a++){
			$data["attr"]=$tmp[$a];
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
		if(sizeof($this->ARRAY_FIELDS_VISIBLE)==0){
			$data["name"]="-No available-";
			$arr[]=$data;
			return $arr;
		}
			
			$attrs[0]=$this->PATH.":".$this->ARRAY_FIELDS_VISIBLE[0]["name"];
			
			for ($a = 1,$b = sizeof($this->ARRAY_FIELDS_VISIBLE);$a < $b;$a++){				
				$attrs[]=$this->ARRAY_FIELDS_VISIBLE[$a]["name"];				
			}
			
		$condition='FILE="'.$ident.'" distinct ';
		
		$result = $this->client->selectAttr($attrs,$condition);
			
		if(!$this->client->eot()){
			
		  while (!$this->client->eot()) {
			for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_VISIBLE);$a < $b;$a++){
				$info=$this->client->getSelectAttrEntry();
				
				if(($this->ARRAY_FIELDS_VISIBLE[$a]["name"]=="Thumb")&&($info[0]!="")){					
					$data["Thumb"]="https://glibrary.ct.infn.it/glibrary_new/glibrary_conexion.php?task=GETTHUMB&rep=".$this->REPOSITORY."&idImg=".$info[0];
				}else{				
				  $info=$this->parseTypeForEdit($a,$info[0],$this->ARRAY_FIELDS_VISIBLE);
				  $data[$this->ARRAY_FIELDS_VISIBLE[$a]["name"]]=$info;
				}
			}
			
			$arr[]=$data;	
		  }
		}else{
			$data["name"]="-No available-";
			$arr[]=$data;
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
		$im = imagecreatefromstring((base64_decode($thumbdata)));
		
		header("Content-type: image/jpg");
		imagejpeg($im);
		imagedestroy($im);

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
			$data["id"]="0";
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
	
	function getValuesFilter($typeFilter){		
		
  	   $attrs=array($typeFilter);	
	   $isArray=$this->IsArray($typeFilter,$this->ARRAY_FIELDS_HIDDEN);		
	   if($isArray)//Take array values.	  
		return($this->getFilterArr($typeFilter));
	   	
	   
		$this->client->selectAttrSQL($attrs, $this->CONDITION,$this->PATH);		
		
		//if(!$this->client->eot()){
		   $rabbish_path=$this->client->getSelectEntrySQL();
		   $data["attr"]="ALL";
		   $arr[]=$data;
		   if(!$this->client->eot()){
		     while (!$this->client->eot()) {			 
			 $valueAtr= $this->client->getSelectEntrySQL();
			 if($valueAtr[0]!="")
				$data["attr"]=$valueAtr[0];				
			 //else				
				//$data["attr"]="-No available-";
				
			 $arr[]=$data;			 
			 $data=null;
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
		$attrs=array("VisibleAttrs","ColumnLabels","ColumnWidth");
		$result= $this->client->getattr($file,$attrs);
		
		$rabbish=$this->client->getSelectAttrEntry();
		///////////////////VISIBLE ATTRIBUTS/////////////
		$filter = $this->client->getSelectAttrEntry();						
		$tokvisibleAt = explode(" ", $filter[0]);
			
		///////////////////EXTRACT COLUMLABELS///////////
		$titlesColums=$this->client->getSelectAttrEntry();			
		$toktitle = explode(",", $titlesColums[0]);
			
		///////////////////EXTRACT COLUMWIDTH////////////
		$widthColums=$this->client->getSelectAttrEntry();									
		$tokWidth = explode(" ", $widthColums[0]);			
			
		///////////////////SET ATTRIBUTOS TITLE, WIDTH, GENERIC, NAME////////////////
		$contTitle=0;
		$contWidth=0;
		for ($a = 0,$b = sizeof($tokvisibleAt);$a < $b;$a++){			
			$data["name"]=$tokvisibleAt[$a];
		   if($tokvisibleAt[$a]=="Thumb"){			
			$data["type"]="image";
			$data["imageHeight"]=120;
			$data["imageWidth"]=100;
		   }
			
			$data["showIf"]="true";
				
			if($tokTitle[$a] !=""){
				$data["title"]=$tokTitle[$a];					
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
			//$this->ARRAY_FIELDS_VISIBLE[]=$data;
			$this->ARRAY_FIELDS_HIDDEN[]=$data;
			$data=null;
		   		
			
		}
			
			
		
		////ADD ATTRIBUT FILE////
		if($isVisibleFILE==false){
				
			$dataKey["name"]="FILE";				
			$dataKey["showIf"]="true";
			$dataKey["primaryKey"]="true";	
			$dataKey["width"]=50;
			$arrayVisible[]="FILE";
			//$this->ARRAY_FIELDS_VISIBLE[]=$dataKey;
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
		//echo "hola--".$nameFilter.json_encode($arrayFields);
		//$this->selectFieldsbyTypes();
		//echo "----------------".json_encode($arrayFields)."---------------------";
		
		while ($find==false){
			$name=$arrayFields[$a]["name"];
			
			if($name==$nameFilter){
				$type=$arrayFields[$a]["type"];
				//echo $type;
				if(($type=="varchar[]")||($type=="textArea")){
					
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
		
		$this->client->selectAttrSQL($attrs, $this->CONDITION,$this->PATH);	
		
		if(!$this->client->eot()){
			$rabbish_path=$this->client->getSelectEntrySQL();
			
		  while (!$this->client->eot()) {			  
			  $IsThumb=false;
			  $array_returned=$this->client->getSelectEntrySQL();
			for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_HIDDEN);$a < $b;$a++){			
				
				if(($this->ARRAY_FIELDS_HIDDEN[$a]["name"]=="Thumb")&&($array_returned[$a]!="")){				
						
						$data["Thumb"]="https://glibrary.ct.infn.it/glibrary_new/glibrary_conexion.php?task=GETTHUMB&rep=".$this->REPOSITORY."&idImg=".$array_returned[$a];
									
				}else{
				  $info=$this->parseType($a,$array_returned[$a],$this->ARRAY_FIELDS_HIDDEN);
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
		for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_HIDDEN);$a < $b;$a++){
				
				$attrs[]=$this->PATH.".".$this->ARRAY_FIELDS_HIDDEN[$a]["name"];
				
		}	
		if($this->CONDITION==" ")
			$condition=" WHERE ";
		else
			$condition=$this->CONDITION." AND ";
		$condition=$condition."/EELA/Entries.FILE = /EELA/Collections.EntryID AND /EELA/Collections.CollID = ".$this->FILEID;
		$this->client->selectAttrSQL($attrs, $condition,"/EELA/Entries, /EELA/Collections");	
		
		if(!$this->client->eot()){
			
			//for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_HIDDEN);$a < $b;$a++){
				$rabbish_path=$this->client->getSelectEntrySQL();	
			//}
		  while (!$this->client->eot()) {			  
			  $IsThumb=false;
			  $array_returned=$this->client->getSelectEntrySQL();
			for ($a = 0,$b = sizeof($this->ARRAY_FIELDS_HIDDEN);$a < $b;$a++){			
				
				if(($this->ARRAY_FIELDS_HIDDEN[$a]["name"]=="Thumb")&&($array_returned[$a]!="")){				
						
						$data["Thumb"]="https://glibrary.ct.infn.it/glibrary_new/glibrary_conexion.php?task=GETTHUMB&rep=".$this->REPOSITORY."&idImg=".$title[0];
									
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
		if(($type=="varchar[]")||($type=="textArea")){			
			
			$tokArray = split('",|",|","|,\S|{"*|"*}', $title);//convert string to array,clean characters "{ and }"
			$data="";
			$long=sizeof($tokArray);
			for ($a = 1,$b = $long-1;$a < $b;$a++){				
				$data=$data.$tokArray[$a]." <br> ";	//make apropiate string, separator <br>			
			}
			
			return $data;
		
		}else		
			return $title;
	
	}
	/**
	* Parse data type varchar [], convert format to correcto text format
	* @access private
	* @param int attribut position in ARRAY_FIELD, string with data varchar[]
	* @return void
	*/
	private function parseTypeForEdit($position,$title,$arrayFields){		
		$type=$arrayFields[$position]["type"];
		if($type=="textArea"){			
			
			$tokArray = split('",|",|","|,\S|{"*|"*}', $title);//convert string to array,clean characters "{ and }"
			$data="";
			//echo $title;
			$long=sizeof($tokArray);
			for ($a = 1,$b = $long-2;$a < $b;$a++){				
				$data=$data.$tokArray[$a]."\n";	//make apropiate string, separator <br>			
			}
			$data=$data.$tokArray[$b];
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
		$attrs=array($path.":FILE");
		$this->client->selectAttr($attrs,$condition);		
		if(!$this->client->eot()){							
			$tmp=$this->client->getSelectAttrEntry();
			$this->FILEID=$tmp[0];
			return true;
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
		return $arr;
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
			
			return true;
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
			
			$attrs = array_merge($attrGeneric, array("OWNER","PERMISSIONS", "GROUP_RIGHTS"));
			
			for ($a = 0,$b = sizeof($attrs);$a < $b;$a++){
				$position=array_search($attrs[$a],$arrayVisible);//get attribute position or false if it's not visible
				
				if($position===false){
					
					//hidden attributs
					 $data["name"]=$attrs[$a];
					 $data["width"]=150;
					 $data["type"]=$types[$a];
					 if($attrs[$a]=="Thumb"){			
						 $data["type"]="image";
						 $data["imageHeight"]=120;
						 $data["imageWidth"]=100;
					 }
					
						 //$data["type"]=$types[$a];
					 $data["showIf"]="false";				
					
					 ////all visible attributs					
					 $this->ARRAY_FIELDS_HIDDEN[]=$data;					
					 //$data["showIf"]="true";
					 //$this->ARRAY_FIELDS_VISIBLE[]=$data;
					 $data=null;
					 
				}else {//put type to visible attributes
					if($attrs[$a]!="Thumb"){
						$this->ARRAY_FIELDS_HIDDEN[$position]["type"]=$types[$a];
						//$this->ARRAY_FIELDS_VISIBLE[$position]["type"]=$types[$a];
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
		
		///////////////////////LISTATTRS//////////////////////////////////////		
		list($attrs,$types)=$this->client->listAttr($this->PATH);
			$attrs = array_merge($attrs, array("OWNER","PERMISSIONS", "GROUP_RIGHTS"));
			$dataKey["name"]="FILE";				
			$dataKey["showIf"]="true";
			$dataKey["primaryKey"]="true";	
			$dataKey["width"]=50;
			$this->ARRAY_FIELDS_VISIBLE[]=$dataKey;
			for ($a = 0,$b = sizeof($attrs);$a < $b;$a++){				
				 $arrProperties=$this->setFieldsProperties($types[$a]);
				 
				 $data["name"]=$attrs[$a];				 
				 $data["width"]=$arrProperties[0];								 
				 $data["type"]=$arrProperties[1];
				 
				 if($attrs[$a]=="Thumb"){			
					 $data["type"]="image";
					 $data["imageHeight"]=120;
					 $data["imageWidth"]=100;
					 
				 }
				 
				 $data["showIf"]="true";
				 if(in_array($attrs[$a],$this->GENERIC_ATTR)==false){
					 $data["generic"]="false";
				 }else $data["generic"]="true";
					
				 ////all visible attributs				 
				 $this->ARRAY_FIELDS_VISIBLE[]=$data;
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
			for ($a = 0,$b = sizeof($attrs);$a < $b;$a++){
				$position=array_search($attrs[$a],$arrayVisible);//get attribute position or false if it's not visible
				if($position===false){
					//hidden attributs
					 $data["name"]=$attrs[$a];
					// $data["width"]=$this->setFieldsWidth($types[$a]);
					$data["width"]=150;
					 $data["type"]=$types[$a];
					 if($attrs[$a]=="Thumb"){			
						 $data["type"]="image";
						 $data["imageHeight"]=120;
						 $data["imageWidth"]=100;
						 
					 }
					
						 //$data["type"]=$types[$a];
					 $data["showIf"]="false";
					 if(in_array($attrs[$a],$this->GENERIC_ATTR)==false){
						 $data["generic"]="false";
					 }else $data["generic"]="true";
					
					 ////all visible attributs					
					 $this->ARRAY_FIELDS_HIDDEN[]=$data;					
					 //$data["showIf"]="true";
					 //$this->ARRAY_FIELDS_VISIBLE[]=$data;
					 $data=null;
					 
				}else {//put type to visible attributes
					if($attrs[$a]!="Thumb"){
						$this->ARRAY_FIELDS_HIDDEN[$position]["type"]=$types[$a];
						//$this->ARRAY_FIELDS_VISIBLE[$position]["type"]=$types[$a];
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
	function setCondition($record,$filter){
		$this->CONDITION=" ";
		$flagEmpty=true;
		for ($a = 0,$b = sizeof($filter);$a < $b;$a++){
			
			if(($filter[$a]!="--")&&($record[$a]!="--")&&($record[$a]!="-No available-")&&($record[$a]!="ALL")){
				$IsArray=$this->IsArray($filter[$a],$this->ARRAY_FIELDS_HIDDEN);
				
			   if($IsArray){
				 $condition="'".$record[$a]."' =ANY(".$this->PATH.".".$filter[$a].")";  
			   }else{
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
		/*if(!$this->client->eot())
			echo "ciao";
		else echo "ciao-hi";
		
		echo $buff[0]."hola";*/
		
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
	
	function setFieldsProperties($type){
		
		//if(in_array("varchar(",$type)==true){
		//$tokArray = split('",|",|","|,\S|{"*|"*}', $title);
		$properties[0]=150;
		$properties[1]=$types;
		
		$tok = split("varchar\(|\)",$type );		
		if($tok!=false)
			$properties[0]=100+(int)$tok[1];		
			
		if(($type=="varchar")||($type=="varchar[]")){
			$properties[0]=200;
			$properties[1]="textArea";
		}
					
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
	* Method:set Fiels, all attributs visible																																																																																																												 
	* @access public
	* @return array fields
	*/
	function setVisibleFields($arr){
		$this->ARRAY_FIELDS_VISIBLE=$arr;	
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
		echo json_encode($this->setFieldsProperties("varchar[]"));
		echo "hola";
	}
	
	
}
?>
