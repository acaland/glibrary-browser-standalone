<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
body {
	margin: 0;
	padding: 0;
	text-align: center;
	color: #000000;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 100%;
}
.oneColElsCtr #container {
	width: 100%;
	text-align: left;
	height: 160px;
	margin-top: 0;
	margin-right: 20px;
	margin-bottom: 0;
	margin-left: auto;
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: solid;
	border-left-style: none;
	float: left;
	background-color: #FFFFFF;
	background-image: url(img/bgdrdr.jpg);
	border-bottom-width: 1px;
	border-bottom-color: #666600;
}
.oneColElsCtr #menu {
	padding-top: 0;
	padding-right: 20px;
	padding-bottom: 0;
	padding-left: 20px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10pt;
	color: #FF9900;
	text-align: left;
	vertical-align: top;
	border-top-width: 2px;
	border-bottom-width: 2px;
	border-top-style: solid;
	border-bottom-style: solid;
	border-top-color: #CCCCCC;
	border-bottom-color: #CCCCCC;
	background-color: #FBFBFB;
}
-->
</style>
</head>

<body class="oneColElsCtr">

<div id="container">
	<table width="100%" border="0" cellspacing="3">
      <tr>
        <td><img src="img/drdr.png" alt="drdr" width="258" height="105" /></td>
        <td><div align="right"><img src="img/infn.png" alt="infn" width="90" height="88" /> <img src="img/egee.png" alt="egee" width="110" height="62" /> <img src="img/gLite.png" alt="gLite" width="110" height="67" /> <img src="img/cometa.png" alt="cometa" width="80" height="80" /><img src="img/lettere.png" alt="facoltà di lettere" width="80" height="79" /> <img src="img/lamusa.png" alt="lamusa" width="138" height="40" /></div></td>
      </tr>
    </table>
  <div id="menu">
  <table width="600" border="0" cellpadding="0" cellspacing="0" id="tablemenu">
  <tr>
    <td><img src="img/about.png" alt="about"  width="60" height="30" />about </td>
    <td><img src="img/browse.png" alt="browse" width="60" height="30" />browse </td>
    <td><img src="img/search.png" alt="search" width="60" height="30" />search</td>
    <?php
    	session_start();
    	if (!$_SESSION['guest']) 
    		echo "<td><a href=\"upload.php\" target=\"_parent\"><img src=\"img/upload.png\" alt=\"upload\" width=\"60\" height=\"30\" /></a>upload</td>";
    ?>
    <td><a href="logout.php" target="_parent"><img src="img/search.png" alt="logout" width="60" height="30" /></a>logout</td>
  </tr>
</table>
</div></div>
    <!-- end #mainContent -->

</body>
</html>
