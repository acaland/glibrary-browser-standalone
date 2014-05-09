<?php

require "glibrary.class.php";

global $glibrary;
$certKey = '/etc/grid-security/hostcertkey.pem';
$caPath = '/etc/grid-security/certificates';
$userName='jsevilla';
//$glibrary=new GLibrary('glibrary.ct.infn.it',8822,'jsevilla','/tmp/x509up_u504','deroberto','../glibrary/classes/certificates');
$glibrary=new GLibrary('glibrary.ct.infn.it',8822,$userName,$certKey,'deroberto',$caPath);

?>