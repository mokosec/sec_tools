<?php
	/*	@mokosec
		Yet Another Conversion Page
	*/
	$header_content ="<!DOCTYPE html>\n<html>\n<head>\n<title>Yet Another Conversion Page</title>\n";
	$header_content .='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	</head><body>';
	$footer .= "</body></html>\n";
	$form_start = '<form accept-charset="utf-8" action="php_converters.php" method="post" enctype="multipart/form-data">';
	$form_end = "</form>";
	$decoded = "";

	// Post -Run function and display
	if(isset($_POST['submit_base64']))	{
		$decoded = base64_enc($_POST['base64_enc']);
	}
	if(isset($_POST['submit_base64_decode']) && !empty($_POST['base64']))	{
		$decoded = base64($_POST['base64']);
	}
	if(isset($_POST['submit_url_decode']))	{
		$decoded = url_decode($_POST['url_decode']);
	}
	if(isset($_POST['submit_hexascii_decode']))	{
		$decoded = hexascii_decode($_POST['hexascii_decode']);
	}
	if(isset($_POST['submit_hexascii_encode']))	{
		$decoded = hexascii_encode($_POST['hexascii_encode']);
	}
	if(isset($_POST['submit_decimal_decode']))	{
		$decoded = decimal_decode($_POST['decimal_decode']);
	}
	if(isset($_POST['submit_base64wide_decode']))	{
		$decoded = base64wide_decode($_POST['base64wide_decode']);
	}
	if(isset($_POST['submit_base64wide_encode']))	{
		$decoded = base64wide_encode($_POST['base64wide_encode']);
	}
	if(isset($_POST['submit_base64_sig']))	{
		$decoded = base64_sig($_POST['base64_sig']);
	}
	if(isset($_POST['submit_zlib_b64']))	{
		$decoded = zlib_b64($_POST['zlib_b64']);
	}
	if(isset($_POST['submit_gzip_b64']))	{
		$decoded = gzip_b64($_POST['gzip_b64']);
	}

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' )	{
		$content = "<br><textarea rows=\"20\" cols=\"130\" name=\"\">$decoded</textarea>\n";
		echo $header_content;
		echo $content;
		echo $footer;
	}

	// Initial Display
	else	{
		$content .= $form_start;
		// Base64 encode
		$content .= "<br><br><a>Base64 Encode</a><br><textarea rows=\"4\" cols=\"137\" name=\"base64_enc\"></textarea>\n";
		$content .= "<input type=\"submit\" name=\"submit_base64\" value=\"submit\"/><br><br>\n";
		// Base64 decode and custom alphabet
		$content .= "<a>Base64 Decode</a><br><textarea rows=\"4\" cols=\"137\" name=\"base64\"></textarea><br><br>\n";
		$content .= "<a>Custom Alphabet</a><br><input type=\"text\" name=\"custom_alphabet\" size=\"102\" value=\"\">\n";
		$content .= "<input type=\"submit\" name=\"submit_base64_decode\" value=\"submit\"/><br>\n";
		// URL decode
		$content .= "<br><br><a>URL Decode</a><br><textarea rows=\"4\" cols=\"137\" name=\"url_decode\"></textarea>\n";
		$content .= "<input type=\"submit\" name=\"submit_url_decode\" value=\"submit\"/><br>\n";
		// Hexascii decode
		$content .= "<br><br><a>Hexascii Decode</a><br><textarea rows=\"4\" cols=\"137\" name=\"hexascii_decode\"></textarea>\n";
		$content .= "<input type=\"submit\" name=\"submit_hexascii_decode\" value=\"submit\"/><br>\n";
		// Hexascii encode
		$content .= "<br><br><a>Hexascii Encode</a><br><textarea rows=\"4\" cols=\"137\" name=\"hexascii_encode\"></textarea>\n";
		$content .= "<input type=\"submit\" name=\"submit_hexascii_encode\" value=\"submit\"/><br>\n";
		// Decimal decode
		$content .= "<br><br><a>Decimal Decode -use any single character non-numerical delimiter</a><br><textarea rows=\"4\" cols=\"137\" name=\"decimal_decode\"></textarea>\n";
		$content .= "<input type=\"submit\" name=\"submit_decimal_decode\" value=\"submit\"/><br>\n";
		// base64wide_decode
		$content .= "<br><br><a>Base64wide (Powershell utf16le decode)</a><br><textarea rows=\"4\" cols=\"137\" name=\"base64wide_decode\"></textarea>\n";
		$content .= "<input type=\"submit\" name=\"submit_base64wide_decode\" value=\"submit\"/><br>\n";
		// base64wide_encode
		$content .= "<br><br><a>Base64wide encode -creates powershell utf16le encoding (I think)</a><br><textarea rows=\"4\" cols=\"137\" name=\"base64wide_encode\"></textarea>\n";
		$content .= "<input type=\"submit\" name=\"submit_base64wide_encode\" value=\"submit\"/><br>\n";
		// base64_sig
		$content .= "<br><br><a>Base64 sig create - needs ascii 10+ char string since it cuts from the start</a><br><textarea rows=\"4\" cols=\"137\" name=\"base64_sig\"></textarea>\n";
		$content .= "<input type=\"submit\" name=\"submit_base64_sig\" value=\"submit\"/><br>\n";
		// zlib_b64
		$content .= "<br><br><a>Decode Base64 encoded Zlib</a><br><textarea rows=\"4\" cols=\"137\" name=\"zlib_b64\"></textarea>\n";
		$content .= "<input type=\"submit\" name=\"submit_zlib_b64\" value=\"submit\"/><br>\n";
		// gzip_b64
		$content .= "<br><br><a>Decode Base64 encoded Gzip</a><br><textarea rows=\"4\" cols=\"137\" name=\"gzip_b64\"></textarea>\n";
		$content .= "<input type=\"submit\" name=\"submit_gzip_b64\" value=\"submit\"/><br>\n";


		$content .= $form_end;
		echo $header_content;
		echo $content;
		echo $footer;
	}

#################################

function base64wide_decode($string)	{
	// Converts powershell utf16le to utf8 -Strips the nulls from powershell type wide base64 decodes
	$decoded = base64_decode($string);
	$decoded = mb_convert_encoding($decoded, 'UTF-8', 'UTF-16LE');

	return $decoded;
}

#################################
function base64wide_encode($string)	{

	// Creates a base64 string utfle 16 like powershell
	$decoded = mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');
	$decoded = base64_encode($decoded);
	return $decoded;
}

#################################
function base64_sig($string)	{
	// Creates a base64 3 part signature from a string
	$string = substr($string, 3);
	$decoded = $string."\n";
	$decoded .= base64_encode(substr($string, 1))."\n";
	$decoded .= base64_encode(substr($string, 2))."\n";
	$decoded .= base64_encode(substr($string, 3))."\n";
	// Wide Version
	$decoded .= "\nWide UTF16LE Versions\n\n";
	$wide_string = substr($string, 1);
	$decoded .= base64_encode(mb_convert_encoding($wide_string, 'UTF-16LE', 'UTF-8'))."\n";
	$wide_string = substr($string, 2);
	$decoded .= base64_encode(mb_convert_encoding($wide_string, 'UTF-16LE', 'UTF-8'))."\n";
	$wide_string = substr($string, 3);
	$decoded .= base64_encode(mb_convert_encoding($wide_string, 'UTF-16LE', 'UTF-8'))."\n";

	return $decoded;
}

#################################
function zlib_b64($string)	{
	$decoded = zlib_decode(base64_decode($string));
	return $decoded;
}

#################################
function gzip_b64($string)	{
	$decoded = gzdecode(base64_decode($string));
	return $decoded;
}
#################################
#################################
function decimal_decode($string)	{
	if(preg_match("/([^\d])/",$string,$matches))	{
		$delim = $matches[1];
	}
	if($delim)	{
		$array = explode($delim, $string);
		foreach ($array as $value)	{
			$decoded .= chr($value);
		}
	}
	else	{
		for ($i=0; $i < strlen($string); $i = $i+3)	{
			$decoded .= chr(substr($string, $i, 3));
		}
	}
	return $decoded;
}

#################################
function hexascii_encode($string)	{

	for ($i=0; $i < strlen($string); $i = $i+1)	{
		$temp_decoded = dechex(ord(substr($string, $i, 1)));
		if (strlen($temp_decoded) == 1)	{		// add leading zeros
			$temp_decoded = "0".$temp_decoded;
		}
		$decoded .= $temp_decoded;
	}
	return $decoded;
}

#################################
function hexascii_decode($string)	{
	for ($i=0; $i < strlen($string); $i = $i+2)	{
		$decoded .= chr(hexdec(substr($string, $i, 2)));
	}
	return $decoded;
}

#################################
function url_decode($string)	{
	$decoded = urldecode($string);
	return $decoded;
}


#################################
function base64_enc($string)	{
	$decoded = base64_encode($string);

	return $decoded;
}

#################################
function base64($string)	{

	if (isset($_POST['custom_alphabet']) and $_POST['custom_alphabet'] != "")	{
		$clen = strlen($_POST['custom_alphabet']);
		$decoded = "Using custom alphabet length:$clen  ".$_POST['custom_alphabet']."\n\n";
		$standard_alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
		// assumes padding is "=" and adds it
		if (strlen($_POST['custom_alphabet']) == 64)	{
			$padstring = "=";
			$custom_alphabet = $_POST['custom_alphabet']."=";
		}
		elseif (strlen($_POST['custom_alphabet']) == 65)	{
			$custom_alphabet = $_POST['custom_alphabet'];
			$padstring = substr($custom_alphabet, -1); 
		}
		else	{
			$custom_alphabet = $standard_alphabet;
			$decoded .= "invalid custom alphabet length - using standard\n";
		}
		// Check text length and correct or pad
		$remainder = $string % 4;
		# b64 text len mod 4 remainder 1 is invalid so remove one character
		if ($remainder == 1)	{
			$string = substr($string, 0, -1);
		}
		# if is is not padded then attempt to add padding
		if ($remainder > 1)	{
			$padlength = 4 - $remainder;
			$padding = str_repeat($padstring,$padlength);
			$string .= $padding;
		}
		// transform
		$string = strtr($string, $custom_alphabet, $standard_alphabet);
	}

	$decoded .= base64_decode($string);
	return $decoded;
}

?>
