<?php 
	$src = $_GET['surl'];	
	//var_dump($src);
	$client_ip = $_SERVER['REMOTE_ADDR'];
	$surl = $src .  "?authip=$client_ip";
	//echo $surl;
	$ch = curl_init();

	//curl_setopt($ch, CURLOPT_VERBOSE, true); 
	curl_setopt($ch, CURLOPT_URL, $surl);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSLCERT, '/etc/grid-security/robot/usercert.pem');
	curl_setopt($ch, CURLOPT_SSLKEY, '/etc/grid-security/robot/userkey.pem');
	//curl_setopt($ch, CURLOPT_SSLKEYPASSWD, 'nonlaso');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$header = curl_exec($ch);
	preg_match('/Location:(.*?)\n/', $header, $matches);
	$newurl = $matches[0];
	//$newurl = trim(array_pop($matches));
	//echo $newurl;
	//header("Location: http://www.google.it");
	header($newurl);
	curl_close($ch);

?> 
