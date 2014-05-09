<?php
		
		
		$ch = curl_init();
		$surl = urldecode($_GET['surl']);
		$src = str_replace("https://", "", $surl);
		$url = "http://glibrary.ct.infn.it/dm/default/" . $src;
		//echo $url;
		$ip = $_SERVER['REMOTE_ADDR'];
		//echo $url;
		//header('Location: ' . $url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("X_FORWARDED_FOR: $ip")); 
		//$response = curl_getinfo( $ch );
		
		//echo $redirect_url;
		$resp = curl_exec($ch);
		//echo $response;
		list($headers, $response) = explode("\r\n\r\n", $resp, 2);
		//echo $headers;
		$headers = explode("\n", $headers);
		foreach($headers as $header) {
    		if (stripos($header, 'Location:') !== false) {
        		header($header);
        		//echo "The location header is: '$header'";
    		}
		}
		//echo $response;
		//$headers = get_headers($response['url']);
		//echo "<br>headers:". curl_getinfo($ch);
		//echo $http_response_header;
		//echo $headers;
		//$redirect_url = $headers[4];
		curl_close($ch);
		//header($redirect_url);
?>
