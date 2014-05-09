<HTML>
<HEAD>
<?php 
	if ($_GET['login'] == 'guest') {
		session_start();
		$_SESSION['login'] = 'guest';
		$_SESSION['guest'] = true;
		//error_log(print_r('set login as guest', true), 3, "/tmp/postErr.log");
		$_SESSION['vo']=$_POST['vo'];
		$command = '/opt/glite/bin/voms-proxy-init --voms cometa -key /etc/grid-security/robot/userkey.pem -cert /etc/grid-security/robot/usercert.pem -out /tmp/x509up_u48';
		$result = exec($command, $out, $ret);
		//error_log(print_r($command. '\n' . $out . '\n' . $ret . '\n'. $result, true), 3, "/tmp/postErr.log");
		header("Location:http://glibrary.ct.infn.it/glibrary_new/browse.php");
	} else {
	//error_log(print_r($_POST, true), 3, "/tmp/postErr.log");
		if (empty($_POST['username'])) {
			header("Location:index.php?error=missing");
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
			}		
		
			$dn = $client->listCred($_POST['username']);
			//error_log(print_r($dn[1], true), 3, "/tmp/postErr.log");
			if (!isset($dn[1])) {
				header("Location:index.php?error=invalid");
			} else {
				$lista_dn = split('\', \'', $dn[1]);
				for ($i = 0; $i < count($lista_dn); $i++)
					$lista_dn[$i] = trim($lista_dn[$i],"'");
				//error_log(print_r($lista_dn, true), 3, "/tmp/postErr.log");
				error_log(print_r($_POST['certificate'], true), 3, "/tmp/postErr.log");
				if (in_array($_POST['certificate'], $lista_dn)) {
					//error_log(print_r("trovato\n", true), 3, "/tmp/postErr.log");
					session_start();
					$_SESSION['login']=$_POST['username'];
					$_SESSION['vo']=$_POST['vo'];
					$_SESSION['guest']=false;
					header("Location:http://glibrary.ct.infn.it/glibrary_new/browse.php");
				} else
					header("Location:index.php?error=nonmatching");
					//error_log(print_r("non trovato\n", true), 3, "/tmp/postErr.log");
					//error_log(print_r($lista_dn, true), 3, "/tmp/postErr.log");
			}
		}
	}
	 

?>
</HEAD>
<BODY>
</BODY>
</HTML>
