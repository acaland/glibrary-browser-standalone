<HTML>
<HEAD>
<SCRIPT>var isomorphicDir="isomorphic7rc2/";</SCRIPT>
<SCRIPT SRC=isomorphic7rc2/system/modules/ISC_Core.js></SCRIPT>
<SCRIPT SRC=isomorphic7rc2/system/modules/ISC_Foundation.js></SCRIPT>
<SCRIPT SRC=isomorphic7rc2/system/modules/ISC_Containers.js></SCRIPT>
<SCRIPT SRC=isomorphic7rc2/system/modules/ISC_Grids.js></SCRIPT>
<SCRIPT SRC=isomorphic7rc2/system/modules/ISC_Forms.js></SCRIPT>
<SCRIPT SRC=isomorphic7rc2/system/modules/ISC_DataBinding.js></SCRIPT>
<SCRIPT SRC=isomorphic7rc2/skins/Enterprise/load_skin.js></SCRIPT>


<?php 

if (isset($_GET['login'])) {

	if ($_GET['login'] == 'guest') {
		session_start();
		$_SESSION['login'] = 'cometa';
		$_SESSION['guest'] = true;
		//error_log(print_r('set login as guest', true), 3, "/tmp/postErr.log");
		$_SESSION['vo']=$_POST['vo'];
		$command = '/opt/glite/bin/voms-proxy-init --voms cometa -key /etc/grid-security/robot/userkey.pem -cert /etc/grid-security/robot/usercert.pem -out /tmp/x509up_u48';
		$result = exec($command, $out, $ret);
		//error_log(print_r($command. '\n' . $out . '\n' . $ret . '\n'. $result, true), 3, "/tmp/postErr.log");
		header("Location:browse.php");
	} 
	else
	{
	
	//error_log(print_r($_POST, true), 3, "/tmp/postErr.log");
	
	if (empty($_POST['username'])) {
		header("Location:login.php?error=missing");
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
			header("Location:login.php?error=invalid");
		} else {
		$lista_dn = split('\', \'', $dn[1]);
		for ($i = 0; $i < count($lista_dn); $i++)
			$lista_dn[$i] = trim($lista_dn[$i],"'");

		if (in_array($_POST['certificate'], $lista_dn)) {
			//error_log(print_r("trovato\n", true), 3, "/tmp/postErr.log");
			session_start();
			$_SESSION['login']=$_POST['username'];
			$_SESSION['vo']=$_POST['vo'];
			$_SESSION['guest']=false;
			header("Location:browse.php");
		} else
			header("Location:login.php?error=nonmatching");
			//error_log(print_r("non trovato\n", true), 3, "/tmp/postErr.log");
		//error_log(print_r($lista_dn, true), 3, "/tmp/postErr.log");
		}
	}
	
	}
	
	
	
	//	 
}
?>
</HEAD>




<BODY>
<SCRIPT>






<?php

		

	//error_log(print_r($_POST, true), 3, "/tmp/postErr.log");
	//error_log(print_r($_GET['login'], true), 3, "/tmp/postErr.log");
	
	if (!isset($_GET['login'])) {
		$tempf = tempnam("/tmp", "cer");
		$handle = fopen($tempf, "w");
		$data = $_SERVER['SSL_CLIENT_CERT']; 
		fwrite($handle, $data);
		fclose($handle);
		$command = '/usr/bin/openssl x509 -in ' . $tempf . ' -nameopt oneline -subject -noout';
		$result = exec($command, $out, $ret);
	//error_log(print_r($command, true), 3, "/tmp/postErr.log");
		unlink($tempf);
	}
?>



isc.DynamicForm.create({
    ID: "loginForm",
    width: 250,
    top: 100,
    left: 100,
    canSubmit:true,
    action: 'login.php',
    fields: [
        {name: "DN",
         
         type: "text",
         
         width: 600,
         defaultValue: "<?php echo $_SERVER['SSL_CLIENT_S_DN']; ?>"
        },
        {name: "certificate",
         title: "DN",
         type: "text",
         width: "600",
         defaultValue: "<?php echo substr($out[0], 9); ?>"
        },
        {name: "username",
         title: "Username",
         required: true,
         type: "text"
         //defaultValue: ""
        },
        {name: "vo",
         title: "Virtual Organization",
         required: true,
         type: "text",
         defaultValue: "cometa"
        },
       
        
        
    ]
});

isc.Button.create({
  	top: 300,
  	left: 100,
    title: "Login",
    click: "loginForm.setAction('login.php?login=true'), loginForm.submit()"
    
});

isc.Button.create({
	top: 300,
    left: 300,
    title: "Login as guest",
    click: "loginForm.setAction('login.php?login=guest'), loginForm.submit()"
    
});

isc.Label.create({
	top: 200,
	left: 300,
	width: 300,
	contents: "<? if (isset($_GET['error'])) 
		switch ($_GET['error']) {
			case 'missing': 
				echo "<B>Please type in a username</B>";
				break;
			case 'invalid':
				echo "<B>The username is invalid</B>";
				break;
			case 'nonmatching':
				echo "<B>The given username doesn't match your DN</B>";
				break;
			case 'dologin':
				echo "<B>Log in first!</B>";
				break;
		}
	?>"

});

</SCRIPT>
</BODY>

