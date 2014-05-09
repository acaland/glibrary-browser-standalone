<?php

$callback = $_GET['callback'];

$se_url_with_token = 'https://aliserv6.ct.infn.it/dpm/ct.infn.it/prova.jpg?token=abcdef';

header('Content-type: text/javascript');
header("Cache-Control: no-cache, must-revalidate");

echo $callback;
echo '({\'action\': \'' . $se_url_with_token . '\'});';

?>