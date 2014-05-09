<?
require_once "mdclient.php";

session_start();

$client = unserialize($_SESSION['connection']);
var_dump($client);

		
?>