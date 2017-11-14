<?php
	//	@mokosec
	//	Using php_curl to do standard GET requests over http and https
	//

	$header_content ="<!DOCTYPE html>\n<html>\n<head>\n<title>CurlIt</title>\n";
	$header_content .='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	</head><body>';

	$user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0';
	$content = "\n\n<br><form>Request<br><input type=\"text\" name=\"request\" size=\"120\"><br><input type=\"submit\" value=\"Go\"></form>\n";

	if(isset($_GET['request']) and !empty($_GET['request']))	{
		$request = $_GET['request'];

		// Add protocol if missing
		if (!preg_match("/^https?:/",$request))	{
			$request = "http://".$request;
		}
		$file_contents = make_curl_request($request,$user_agent);
		$cert_contents = cert_info($request);

	}

	if($cert_contents)	{
		$content .= "<br><textarea rows=\"40\" cols=\"200\">$file_contents</textarea><br>";
		$content .= "<br><br><a>Certificate Info</a><br><br><textarea rows=\"40\" cols=\"200\">$cert_contents</textarea><br>\n";
	}
	else	{
		$content .= "<br><textarea rows=\"40\" cols=\"200\">$file_contents</textarea><br>\n";
	}

	$content .= "</body></html>\n";

	echo $header_content;
	echo $content;	


#################################
function make_curl_request($url,$user_agent)	{
	
	$rando = generateRandomString();
	$referer = "http://bit.ly/$rando";


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);
	curl_setopt($ch, CURLOPT_REFERER, $referer);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);	// like --insecure 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,4); 	// timeout on connect
	curl_setopt($ch, CURLOPT_TIMEOUT, 4);			// timeout on response

	$file_contents = curl_exec($ch);

    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );

	if ($err or $errmsg)	{
		$file_contents = "$err $errmsg\n".$file_contents;
	}

	curl_close($ch);

	// Clean it
	$file_contents = htmlentities($file_contents);

	return $file_contents;

}

#################################

function cert_info($url)	{

	$orignal_parse = parse_url($url, PHP_URL_HOST);
	$get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
	$read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
	$cert = stream_context_get_params($read);
	$certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
	$cert_detail = "";
	$element = "";

	foreach ( $certinfo as $key => $value	)	{
		if( $key == "validFrom_time_t" or $key == "validTo_time_t" )	{	// redundant stuff
			continue;
		}
		if( is_array($value) )	{
			foreach( $value as $k => $v )	{
				if( is_array($v) )	{
					foreach( $v as $k1 => $v1 )	{
						if( $k1 < 3 )	{		// gets rid of a bunch of time related lines
							continue;
						}
						if( mb_detect_encoding($v1, 'ASCII', true) )	{
							$cert_detail .= "\t\t$k1:\t$v1\n";
						}
					}
				}
				else	{
					if( mb_detect_encoding($v, 'ASCII', true) )	{
						$cert_detail .= "\t$k:\t$v\n";
					}
				}
			}
		}
		else	{
			if( mb_detect_encoding($value, 'ASCII', true) )	{
				$cert_detail .= "$key:\t$value\n";
			}
		}
	}
	return $cert_detail;

}


#################################

function generateRandomString($length = 7) {

    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

	
?>
