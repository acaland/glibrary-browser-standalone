<?php

	session_start();
	$_SESSION['login'] = 'guest';
	echo htmlspecialchars(SID);
?>
