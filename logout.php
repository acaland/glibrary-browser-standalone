<?php
	session_start();
	if ($_SESSION['guest'])
		//unlink('/tmp/x509up_u48');
	session_destroy();
	
	header('Location:index.php');
?>