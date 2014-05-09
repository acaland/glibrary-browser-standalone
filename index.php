<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>INFN gLibrary Homepage</title>
<style type="text/css">
<!--
body {
	margin: 0; /* it's good practice to zero the margin and padding of the body element to account for differing browser defaults */
	padding: 0;
	text-align: center; /* this centers the container in IE 5* browsers. The text is then set to the left aligned default in the #container selector */
	color: #000000;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 100%;
	background-color: #333333;
}

/* Tips for Elastic layouts 
1. Since the elastic layouts overall sizing is based on the user's default fonts size, they are more unpredictable. Used correctly, they are also more accessible for those that need larger fonts size since the line length remains proportionate.
2. Sizing of divs in this layout are based on the 100% font size in the body element. If you decrease the text size overall by using a font-size: 80% on the body element or the #container, remember that the entire layout will downsize proportionately. You may want to increase the widths of the various divs to compensate for this.
3. If font sizing is changed in differing amounts on each div instead of on the overall design (ie: #sidebar1 is given a 70% font size and #mainContent is given an 85% font size), this will proportionately change each of the divs overall size. You may want to adjust based on your final font sizing.
*/
.oneColElsCtrHdr #container {
	width: 100%;  /* this width will create a container that will fit in an 800px browser window if text is left at browser default font sizes */
	background: #FFFFFF; /* the auto margins (in conjunction with a width) center the page */
	border: 0px solid #000000;
	text-align: left; /* this overrides the text-align: center on the body element. */
	margin-top: 0;
	margin-right: auto;
	margin-bottom: 0;
	margin-left: auto;
}
.oneColElsCtrHdr #header {
	padding-top: 0px;
	padding-right: 0%;
	padding-bottom: 0;
	padding-left: 10%;
	background-color: #333333;
	height: 40px;
	vertical-align: middle;
} 

#header a:link {
	color: #999999;
}

#header a:visited{
color: #999999;
}

#header a:hover{
	color: #999999;
}

.oneColElsCtrHdr #header h1 {
	margin: 0; /* zeroing the margin of the last element in the #header div will avoid margin collapse - an unexplainable space between divs. If the div has a border around it, this is not necessary as that also avoids the margin collapse */
	padding: 10px 0; /* using padding instead of margin will allow you to keep the element away from the edges of the div */
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 13px;
	color: #999999;
}
form {
	text-align: center;
	padding-top: 20px;
	padding-right: 20px;
	padding-bottom: 20px;
	padding-left: 30px;
}
.oneColElsCtrHdr #mainContent {
	padding-top: 0px;
	padding-right: 10%;
	padding-bottom: 0;
	padding-left: 10%;
	background-color: #FFFFFF;
	background-image: url(img/texture.png);
}
.oneColElsCtrHdr #footer {
	padding-top: 0;
	padding-right: 10%;
	padding-bottom: 0;
	padding-left: 10%;
	background-color: #39456C;
	color: #CCCCCC;
	margin: 0px;
} 
.title {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 22px;
	font-variant: small-caps;
	color: #FFFFFF;
	font-weight: bold;
}
input {
	width: 160px;
}
#login {
	background-color: #CCCCCC;
	border: 1px solid #999999;
	width: 225px;
	text-align: center;
	padding-top: 20px;
	padding-bottom: 20px;
	margin-top: 25px;
	margin-right: 15px;
	margin-bottom: 15px;
	margin-left: 15px;
}
.oneColElsCtrHdr #footer p {
	margin: 0; /* zeroing the margins of the first element in the footer will avoid the possibility of margin collapse - a space between divs */
	padding: 10px 0; /* padding on this element will create space, just as the the margin would have, without the margin collapse issue */
}
.style5 {font-size: 17px; }
.style6 {color: #FF9900}
.style7 {color: #FFFFFF}
-->
</style>
</head>

<body class="oneColElsCtrHdr">

<div id="container">
  <div id="header">
    <h1><a href="#">About the project</a> | <a href="#">Who we are</a> | <a href="#">Documentation</a> | <a href="#">Contact us</a></h1>
  </div>
  <div id="mainContent">
  <div align="center"></div>
  <table width="100%" border="0" cellspacing="0">
  <tr>
    <td width="40%"><h1><img src="img/glibrary.png" alt="gLibrary" /></h1>
      <h1>Digital Libraries<br />
        on the Grid </h1>
      <p class="style5">gLibrary is a tool developed by <a href="http://www.infn.it/indexen.php">INFN</a> 
        to easily <strong>create</strong>, <strong>organize</strong>, <strong>access</strong> 
        <span class="style1 style6"><strong>digital assets</strong></span> on <a href="http://glite.web.cern.ch/glite/">gLite</a>-based 	
        Grid infrastructures.</p>
      <p class="style3"><span class="style5">For more informations, <a href="#">read here</a></span></p>      </td>
    <td width="34%" valign="bottom"><div align="right"><img src="screenshot2.png" alt="glibrary" width="600" height="444" /></div></td>
    <td width="34%" valign="bottom">
    
    
      <table width="220
      " border="0" align="center" cellspacing="0">
        <tr>
          <td><div align="center"><img src="img/piccoli/infn.png" alt="infn" width="35" height="35" /></div></td>
          <td><div align="center"><img src="img/egi_logo.png" alt="egi" width="68" height="38" /></div></td>
          <td><div align="center"><img src="img/emi_logo.png" alt="emi" width="66" height="40" /> </div></td>
        </tr>
      </table>
      
   
    <div id="login">
    <a href="login.php?login=guest"><img src="img/guest.png" alt="login as guest" /></a><br />
      <form id="form1" name="form1" method="post" action="login.php">
        <label>
        <div align="left">username<br />
          <input type="text" name="username" id="user" />
          <br />
          <br />
          virtual organization<br />
          <input type="text" name="vo" id="vo" value="cometa"/>
          <br /><br />	
          <input type="submit" value="Login"/>
          <?php
          	if (isset($_GET['error'])) 
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
	        $tempf = tempnam("/tmp", "cer");
			$handle = fopen($tempf, "w");
			$data = $_SERVER['SSL_CLIENT_CERT']; 
			fwrite($handle, $data);
			fclose($handle);
			$command = '/usr/bin/openssl x509 -in ' . $tempf . ' -nameopt oneline -subject -noout';
			$result = exec($command, $out, $ret);
			//error_log(print_r($command, true), 3, "/tmp/postErr.log");
			unlink($tempf);
		   ?>
          <input type="hidden" name="certificate" value="<?php echo substr($out[0], 9); ?>"/>
        </div>
        </label>
      </form>
      <img src="img/account.png" alt="request an account" width="200" height="70" />      </div>      </td>
  </tr>
</table>
  <!-- end #mainContent -->
  </div>
  <div id="footer">
    <span class="title">NEWS</span> 
    <p><strong>Federico De Roberto works on the Grid with gLibrary</strong></p>
    <p>Federico De Roberto, an italian writer of the XIX/XX century, left many works of relevant values to the humanistic communities. Around 8000 sheets of those works have been digitized by La.m.u.sa team, a group of researchers from the Faculty of Letters and Philosophy in Catania. With gLibrary, the De Roberto digital repository has been created, and this cultural heritage, replicated on several storages in a grid, will be preserved and it is now accessible to everyone.</p>
    
  <!-- end #footer --></div>
<!-- end #container --></div>
<span class="style7" padding-top="25px">Connected user: <?php echo $_SERVER['SSL_CLIENT_S_DN']; ?></span>
</body>
</html>
