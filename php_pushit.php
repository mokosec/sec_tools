<?php
	// Set the include path (set perms not to directly serve from inc) - not good to run perl directly from www
	ini_set('include_path', '/var/www/inc');
	// Set the path to the perl script
	$script_path = "/var/www/inc/pushit.pl";


	$header_content ="<!DOCTYPE html>\n<html>\n<head>\n<title>PHP_PushIt</title>\n";
	$header_content .='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	</head><body>';

	# POST received, run it and display results
	if(isset($_POST['request']) and !empty($_POST['request']))	{
		$request = base64_encode($_POST['request']);
		$host = $_POST['host'];
		$port = $_POST['port'];
		$https = $_POST['https'];

		// Send it to perl for socket action
		exec("/usr/bin/perl $script_path $request host:$host port:$port https:$https", $output, $return_val);

		if($output)	{
			foreach($output as $line)	{
				$string .= $line;
			}
			$string = base64_decode($string);
			$content = "<br><textarea rows=\"40\" cols=\"200\">$string</textarea><br>";
		}
		else	{
			$content = "<br><textarea rows=\"40\" cols=\"200\">Something is wrong, no output from request</textarea><br>\n";
		}	

	}
	// Initial request - show the blank page
	else	{

		$content = "\n\n<br><form accept-charset=\"utf-8\" action=\"php_pushit.php\" method=\"POST\" enctype=\"multipart/form-data\" TARGET=\"_blank\">\n";
		$content .= "<a>Request</a><br><textarea name=\"request\" rows=\"20\" cols=\"120\"></textarea><br>\n";
		$content .= "<a>Port if needed&nbsp;</a><input type=\"text\" name=\"port\" size=\"20\">\n";
		$content .= "<a>Host if needed&nbsp;</a><input type=\"text\" name=\"host\" size=\"50\">\n";
		$content .= "<a>HTTPS</a><input type=\"checkbox\" name=\"https\" value=\"1\">\n";
		$content .= "<input type=\"submit\" value=\"Go\"></form>\n";
	}

	$content .= "</body></html>\n";

	echo $header_content;
	echo $content;	

	
?>
