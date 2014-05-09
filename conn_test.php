<?
require_once "mdclient.php";

session_start();

try {
			$certKey = '/etc/grid-security/hostcertkey.pem';
			$caPath = '/etc/grid-security/certificates';
			$client = new MDClient('glibrary.ct.infn.it', 8822, 'root');
			$client->requireSSL($certKey, $certKey, $caPath);
			$client->connect();
			$_SESSION['connection'] = serialize($client);
			//$this->client->sudo($this->LOGIN);
			var_dump($client);
		} catch (Exception $e) 	{
		echo 'Unable to connect to AMGA: ',  $e->getMessage(), "\n";
			return false;
		}	
		
?>