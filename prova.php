<?php
if (isset($_GET['imagen'])){	
		
	$thum=$_GET['imagen'];
	echo $thum;
}	
	$im = imagecreatefromstring((base64_decode("\/9j\/4AAQSkZJRgABAgAASABIAAD\/7QAMQWRvYmVfQ00AAf\/uAA5BZG9iZQBkgAAAAAH\/2wCEAAwICAgJCAwJCQwRCwoLERUPDAwPFRgTExUTExgRDAwMDAwMEQwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwBDQsLDQ4NEA4OEBQODg4UFA4ODg4UEQwMDAwMEREMDAwMDAwRDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDP\/AABEIAKAAdAMBIgACEQEDEQH\/3QAEAAj\/xAE\/AAABBQEBAQEBAQAAAAAAAAADAAECBAUGBwgJCgsBAAEFAQEBAQEBAAAAAAAAAAEAAgMEBQYHCAkKCxAAAQQBAwIEAgUHBggFAwwzAQACEQMEIRIxBUFRYRMicYEyBhSRobFCIyQVUsFiMzRygtFDByWSU\/Dh8WNzNRaisoMmRJNUZEXCo3Q2F9JV4mXys4TD03Xj80YnlKSFtJXE1OT0pbXF1eX1VmZ2hpamtsbW5vY3R1dnd4eXp7fH1+f3EQACAgECBAQDBAUGBwcGBTUBAAIRAyExEgRBUWFxIhMFMoGRFKGxQiPBUtHwMyRi4XKCkkNTFWNzNPElBhaisoMHJjXC0kSTVKMXZEVVNnRl4vKzhMPTdePzRpSkhbSVxNTk9KW1xdXl9VZmdoaWprbG1ub2JzdHV2d3h5ent8f\/2gAMAwEAAhEDEQA\/APSMkubTU0GBt189Aq481Zyx7ah5f3ILW6Qsvmf56X0\/6LaxfIFoB0\/ApRHbRTAn4p9vioaTaMNH+uiWweA+CKGp9oREVcSD0\/EQkKwjwIS290eFXGUIZ4\/iiBg7wpAKcBDhWmSJzAG8ahR2t8EchQDRJn5JEKEkJaFAMAMIxCgYSpkBWgxyklrHkkkp\/9D0rLEtr\/18EJjRzCNlcV\/A\/wAEMTAjTXXvp4LN5j+en9P+i2IfIGQCcAxqn0nhN2USFAJJBvcDUqW3ThEBVrQY80gNE8JQjSLWAT6pRqpJUolaNIUSI1U4CYjQ\/BAhAKByEUVw8EIjVRs0VToknjRJFc\/\/0fS8niv5\/wAFAAA\/BFv+jWfL+5CafNZ3Mfz0\/p\/0WeHyBmEmhIFOFHSCvEJSmKZFTLzSISCWiSFAapwmHKkkFFZJOmSQheNfBCIRrBrKCVF1ZorJJ+ySS5\/\/0vTb\/o1\/BDHCJf8ARrPaEJqzuY\/npfT\/AKLND5AzCl2UQPJP8lGorlJMnRQum1UCx5uDiRsbwASDJ0M\/mvUCy4Wus9rpBa3nQHbs9v0fp7\/UejSkwUgoMFm0b9pd3LQQPuO5TQCipJJJFCOzhAcfFHfqCq7uYKiluywVIhJRhySavp\/\/0\/S8j6Ffw\/gFBp8kTI\/m6\/P+5DZMH4rN5j+el9P+izw+QM2z3CTnbeZMkDQE8mOyQ+KeZTEKCTtwYSwS6PaOxPZIJ9OEQhbc+NWGRyAR+GqYl26dhIEQZEz37qaYolS253ZpGo1Mcfnd07C46ubt8NZThOkpSSWiSSGDxoqz+VZcVWt0MqOTLjWn70lDcYSTGSn\/1PTMifTr1gR\/BDYDCLk6NYhNkhZvMfz0vp\/0WeHyBmPin10TNGikBHzTAgq+eqQEcJ4STqRa2qXzSPCYIFTL5pwmTohBUmPxTpIFTFBtgj\/YjOBjTlDIMawU09l8d7a+3RJS1mPNJRstv\/\/V9Ny+G6eKEz5ouUYDfiUFpWbzH89L6f8ARZ4fIEggBSBUJHiluj\/ZqmIISJIFmXVVY2six1jpLWsY4zHPu27P+kpMyWP022NPEOrePx27U+pVdFXCeyQwm+SX3pJpQyCdME6IQs9u4RJbqDIMHQ7k5STFIqYlQdwpmFByYvCL87jRJT0hJCvzX2\/\/1vTcrhvzQRKNl8N+aC0jwWbzP89L6f8ARZ4fIGYGnMpwE4TpgQSt5Sl81KE0aootQ8ZTT5p+ybVIpXHxUkwTpBaVJk6ZIqYn4qDuVMlRJ1QXhh37pJ92vKSC7V\/\/1\/TcvhvzVccqxliQ3tyEFqzeZ\/npfT\/otjH8gZtEiSp8KI4TqMLSyCUKI+emnCdOQopoSKQSKVwpKPdODqRBgd+yVoKkjwUj5JdkCpiRooHupkKB4QXBjokoyN0QUkF7\/9D0zMEhs8aquwuBgqeTlYzw0sta8Dna4H8iCMmrxWZzP89L6f8ARbOMEwGltlp0T6+Krtyao0Kn9pr\/AHgowVGEuybtqdUvmhfaavFL7TX4o2FvBLsUqWkIX2ivxSORXp7gkSE8EuyWR48pwUD7TX4qQyGeIQ4ggwl2Syn7IX2ivxS+0Mg6jyR4gjhl2ZkjxUXuCG7Jr8UM5NZ0nVDiXiB7M9xie8pIf2hnE6\/xSQtfwns\/\/9k=")));
	
	header("Content-type: image/jpg");
	imagejpeg($im);
	
	imagedestroy($im);

	
?>
$this->client->requireSSL($this->CERT_KEY, $this->CERT_KEY, $this->CERTIFICATES);
			$this->client->connect();
			$this->sudo($this->LOGIN');
