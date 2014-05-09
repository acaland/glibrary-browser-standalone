<?php 
	if ($_GET['login'] == 'guest') {
		session_start();
		$_SESSION['login'] = 'cometa';
		$_SESSION['guest'] = true;
		//error_log(print_r('set login as guest', true), 3, "/tmp/postErr.log");
		$_SESSION['vo']=$_GET['vo'];
		$command = '/opt/glite/bin/voms-proxy-init --voms cometa -key /etc/grid-security/robot/userkey.pem -cert /etc/grid-security/robot/usercert.pem -out /tmp/x509up_u48';
		$result = exec($command, $out, $ret);
		error_log(print_r($command. '\n' . $out . '\n' . $ret . '\n'. $result, true), 3, "/tmp/postErr.log");
		
		echo json_encode(array('session' => htmlspecialchars(SID)));
		return;
	} else {
		//error_log(print_r($_GET, true), 3, "/tmp/postErr.log");
		if (empty($_GET['login'])) {
			echo json_encode(array('session' => 'null', 'error' => 'login is missing'));
			return;
		} else {	
			require "mdclient.php";

			//error_log(print_r("ci arrivo", true), 3, "/tmp/postErr.log");
			$certKey = '/etc/grid-security/hostcertkey.pem';
			$caPath = '/etc/grid-security/certificates';
			$userName='root';
			try {
				$client = new MDClient('glibrary.ct.infn.it', 8822, 'root','gl1br@r1', false);
				//$client->requireSSL($certKey, $certKey, $caPath);
				$client->connect();
			} catch (Exception $e) 	{
				echo 'Unable to connect to AMGA: ',  $e->getMessage(), "\n";
				echo json_encode(array('session' => 'null', 'error' => 'unable to connect to the AMGA server'));
				return;
			}		
		
			$dn = $client->listCred($_GET['login']);
			//error_log(print_r($dn[1], true), 3, "/tmp/postErr.log");
			if (!isset($dn[1])) {
				echo json_encode(array('session' => 'null', 'error' => 'login invalid'));
				return;
			} else {
				$lista_dn = split('\', \'', $dn[1]);
				for ($i = 0; $i < count($lista_dn); $i++)
					$lista_dn[$i] = trim($lista_dn[$i],"'");
					
				$tempf = tempnam("/tmp", "cer");
				$handle = fopen($tempf, "w");
				$data = $_SERVER['SSL_CLIENT_CERT'];
				//error_log(print_r($data, true), 3, "/tmp/postErr.log");
		
				fwrite($handle, $data);
				fclose($handle);
				$command = '/usr/bin/openssl x509 -in ' . $tempf . ' -nameopt oneline -subject -noout';
				$result = exec($command, $out, $ret);
				unlink($tempf);
				//error_log(print_r($out, true), 3, "/tmp/postErr.log");
		
		        $certificate=substr($out[0], 9);

				if (in_array($certificate, $lista_dn)) {
					//error_log(print_r("trovato\n", true), 3, "/tmp/postErr.log");
					session_start();
					$_SESSION['login']=$_GET['login'];
					$_SESSION['vo']=$_GET['vo'];
					$_SESSION['guest']=false;
					echo json_encode(array('session' => htmlspecialchars(SID)));
					return;
				} else
					echo json_encode(array('session' => 'null', 'error' => "your login name doesn't match your certificate"));
					return;
					//error_log(print_r("non trovato\n", true), 3, "/tmp/postErr.log");
					//error_log(print_r($lista_dn, true), 3, "/tmp/postErr.log");
			}
		}
	}
	 

?>
