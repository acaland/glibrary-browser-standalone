<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>INFN gLibrary homepage</title>
<style type="text/css"> 

body  {
	padding: 0;
	text-align: center;
	color: #000000;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 100%;
	background-color: #E8E6DD;
	margin-top: 10px;
	margin-right: 20px;
	margin-bottom: 10px;
	margin-left: 20px;
}

.thrColHyb #container {
	width: 100%;
	background: #FFFFFF;
	text-align: left;
	border: 3px solid #FFFFFF;
} 
li {
	font-size: 10pt;
	list-style-type: circle;
	margin: 0px;
	padding: 0px;
}

.thrColHyb #sidebar1 {
	float: left;
	width: 13em;
	background: #A2B5CC;
	padding-top: 15px;
	padding-right: 0;
	padding-bottom: 15px;
	padding-left: 0;
	height: 25em;
}
.thrColHyb #sidebar2 {
	float: right;
	width: 13em;
	background: #A2B5CC;
	height: 25em;
	padding-top: 15px;
	padding-right: 0;
	padding-bottom: 15px;
	padding-left: 0;
	text-align: center;
	vertical-align: middle;
}
.thrColHyb #sidebar1 h3, .thrColHyb #sidebar1 p, .thrColHyb #sidebar2 p, .thrColHyb #sidebar2 h3 {
	margin-left: 10px;
	margin-right: 10px;
}


.thrColHyb #mainContent {
	margin-top: 0;
	margin-right: 12em;
	margin-bottom: 0;
	margin-left: 12em;
	padding-top: 1em;
	padding-right: 2em;
	padding-bottom: 0;
	padding-left: 2em;
} 
.thrColHyb #mainContent h1 { 
} 
.fltrt { 
	float: right;
	margin-left: 8px;
}
.fltlft { 
	float: left;
	margin-right: 8px;
}
.clearfloat { 
	clear:both;
    height:0;
    font-size: 1px;
    line-height: 0px;
}
p {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10pt;
}
h3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #003399;
}
</style>
</head>

<body class="thrColHyb">

<div id="container">
  <div id="sidebar1">
    <h3>Project</h3>
    <ul>
  		<li>Lorem ipsum dolor sit amet</li>
    	<li>consectetur adipisicing elit</li>
    	<li>sed do eiusmod tempor incididunt</li> 
    	<li>ut labore et dolore magna aliqua</li>
    </ul>
    <!-- end #sidebar1 --></div>
  <div id="sidebar2">
    <p><img src="img/infn.png" alt="infn" width="90" height="88" /></p>
    <p>&nbsp;</p>
    <p><img src="img/egee.png" alt="egee" width="110" height="62" /></p>
    <p>&nbsp;</p>
    <p><img src="img/gLite.png" alt="gLite" width="110" height="67" />
      <!-- end #sidebar2 -->
    </p>
  </div>
  <div id="mainContent">
    <h1 align="center"><img src="img/glibrary.png" alt="gLibrary" width="250" height="105" /></h1>
    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent aliquam,  justo convallis luctus rutrum, erat nulla fermentum diam, at nonummy quam  ante ac quam. Maecenas urna purus, fermentum id, molestie in, commodo  porttitor, felis. Nam blandit quam ut lacus. </p>
    <p>Quisque ornare risus quis  ligula. Phasellus tristique purus a augue condimentum adipiscing. Aenean  sagittis. Etiam leo pede, rhoncus venenatis, tristique in, vulputate at,  odio. Donec et ipsum et sapien vehicula nonummy. Suspendisse potenti. Fusce  varius urna id quam. Sed neque mi, varius eget, tincidunt nec, suscipit id,  libero. </p>
    <h2>H2 level heading </h2>
    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent aliquam,  justo convallis luctus rutrum, erat nulla fermentum diam, at nonummy quam  ante ac quam. Maecenas urna purus, fermentum id, molestie in, commodo  porttitor, felis. Nam blandit quam ut lacus. Quisque ornare risus quis  ligula. Phasellus tristique purus a augue condimentum adipiscing. Aenean  sagittis. Etiam leo pede, rhoncus venenatis, tristique in, vulputate at, odio.</p>
    <p><?php echo $_SERVER['SSL_CLIENT_S_DN']; ?></p>
  <!-- end #mainContent --></div>
	<br class="clearfloat" />
<!-- end #container --></div>
</body>
</html>
