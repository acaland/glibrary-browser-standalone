<?php
/** 
 *	This is the main class to access LCG infosites through PHP
 *	@package lcg-php
 */
class LCGInfosites
{
	/**
	* Hostname of the BDII server.
	* @access private
	* @var string
	*/
	private $host;

	/**
	* Port on which the BDII server listens for connection.
	* @access private
	* @var integer
	*/
	private $port;

	/**
	* Virtual Organization.
	* @access private
	* @var string
	*/
	private $vo;

	/**
	* LDAP link identifier.
	* @access private
	* @var resource
	*/
	private $link;

	/**
	* Function used to connect to the BDII server.
	* @access public
	* @param string $host AMGA server hostname
	* @param integer $port AMGA server port
	* @param string $vo the virtual organization
	* @return boolean whether the connection was successful
	*/
	public function connect($host, $port, $vo = "")
	{
		$this->host = $host;
		$this->port = $port;
		$this->vo = $vo;

		if (!($this->link = ldap_connect($host, $port)))
			return false;

		if (!@ldap_bind($this->link))
			return false;

		return true;		
	}

	/**
	* Function used to retrieve information related to SEs.
	* @access public
	* @return mixed FALSE in case of error, array of results otherwise
	*/
	public function listSE()
	{
		$filter = "(&(ObjectClass=GlueSA)(GlueSAAccessControlBaseRule=".$this->vo."))";
		$base_dn = "mds-vo-name=local,o=grid";
		//$arr[]="GlueSAPath";
		// execute query
		if (!($sr = ldap_search($this->link, $base_dn, $filter)))
			return FALSE;

		$info = ldap_get_entries($this->link, $sr);

		return $info;
	}

	/**
	* Function used to retrieve information related to CEs.
	* @access public
	* @return mixed FALSE in case of error, array of results otherwise
	*/
	public function listCE()
	{
		$filter = "(&(ObjectClass=GlueCE)(GlueCEAccessControlBaseRule=VO:".$this->vo."))";
		$base_dn = "mds-vo-name=local,o=grid";
		
		// execute query
		if (!($sr = ldap_search($this->link, $base_dn, $filter)))
			return FALSE;

		$info = ldap_get_entries($this->link, $sr);

		return $info;
	}

	/**
	* Function used to retrieve tags of applications installed on CEs.
	* @access public
	* @return mixed FALSE in case of error, array of results otherwise
	*/
	public function listTags()
	{
		$result = array();

		// getting the list of CEs
		$ce_list = $this->listCE();
		$num_results = $ce_list["count"];

		// getting the Tags for each CE
		for ($i = 0; $i < $num_results; $i++) 
		{
			$ce_name = $ce_list[$i]["glueceinfohostname"][0];
			$filter = "(&(objectClass=GlueHostApplicationSoftware)(GlueSubClusterName=".$ce_name."))";
			$base_dn = "mds-vo-name=local,o=grid";
		
			// execute query
			if (!($sr = ldap_search($this->link, $base_dn, $filter)))
				return FALSE;

			array_push($result, ldap_get_entries($this->link, $sr));
		}

		return $result;
	}

	/**
	* Function used to retrieve a single attribute from a DN
	* @access public
	* @return string the value of requested attribute
	*/
	public function getAttributeFromDN($dn, $attribute)
	{
		$subject = "/".$attribute."=([^,]*)/";
		preg_match($subject, $dn, $matches);
		return $matches[1];
	}
}
?>