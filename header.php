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
	height: 100px;
	margin-top: 0;
	margin-right: 20px;
	margin-bottom: 0;
	margin-left: auto;
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
	float: left;
	background-color: #FFFFFF;
	background-image: url(img/texture.png);
}
.oneColElsCtr #menu {
	padding-top: 0;
	padding-right: 20px;
	padding-bottom: 0;
	padding-left: 280px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12pt;
	color: #666666;
	text-align: left;
	vertical-align: top;
	border-bottom-width: 2px;
	border-bottom-style: solid;
	border-bottom-color: #CCCCCC;
	margin-top: -40px;
	font-weight: bold;
}
#menu a:link {
	color: #666666;
	text-decoration: none;
}

#menu a:visited {
	color: #666666;
	text-decoration: none;
}
#menu a:active {
	color: #666666;
	text-decoration: none;
}
#menu a:hover {
	color: #FF6600;
	text-decoration: none;
}
-->
</style>

</head>

<body class="oneColElsCtr">

<div id="container">
	<table width="100%" border="0" cellspacing="3">
      <tr>
        <td><img src="img/glibrary.png" alt="gLibrary" width="250" height="105" /></td>
        <td><div align="right"><img src="img/infn.png" alt="infn" width="40" height="39" /> <img src="img/egee.png" alt="egee" width="66" height="37" /> <img src="img/gLite.png" alt="gLite" width="66" height="40" /></div></td>
      </tr>
    </table>
  <div id="menu">
  <table width="600" border="0" cellpadding="0" cellspacing="0" id="tablemenu">
  <tr>
    <td><a href="#"><img src="img/about.png" alt="about"  width="60" height="30" border="0" />home</a></td>
    <td><a href="#"><img src="img/browse.png" alt="browse" width="60" height="30" border="0" />browse</a><a href="#"></a> </td>
    <td><a href="#"><img src="img/search.png" alt="search" width="60" height="30" border="0" />search</a></td>
    <?php
    	session_start();
    	if (!$_SESSION['guest']) 
    		echo "<td><a href=\"upload.php\" target=\"_parent\"><img src=\"img/upload.png\" alt=\"upload\" width=\"60\" height=\"30\" /></a>upload</td>";
    ?>
    <td><a href="logout.php" target="_parent"><img src="img/logout.png" alt="logout" width="60" height="30" border="0" />logout</a></td>
  </tr>
</table>
</div></div>
    <!-- end #mainContent -->

</body>
</html>
