use IO::Socket::INET;
use IO::Socket::SSL;	#use for debugging qw(debug3);
use MIME::Base64;

# Get the request and ARGs
$request = decode_base64(@ARGV[0]);	# full request
($host 	= @ARGV[1]) =~ s/host://;	# Host or blank
($port 	= @ARGV[2]) =~ s/port://;	# Port or blank
($https	= @ARGV[3]) =~ s/https://;	# 1 or blank


# Extract the hostname if it wasn't sent
if(!$host)	{
	# OPEN as scalar
	open (MEM, "<", \$request) || die("cant open memory file: $!");
	while (<MEM>)	{
		$trimmed = trim($_);
		if($trimmed =~ /Host: (.+)/)	{
			$host = $1;
		}
	}
	close MEM;
}

# Extract the port if it wasn't sent and cleanup the hostname
if ($host =~ /:/)	{
	($host,$tmp_port) = split /:/,$host;
	if(!$port && $tmp_port)	{	# extracted a port and it wasn't set from php
		$port = $tmp_port;
	}
}

# No port and https assume 443
if(!$port && $https)	{
	$port = 443;
}

# No port assume 80
if(!$port)	{
	$port = 80;
}


# Got everything, lets go
$request_content = socket_ops($request);

$request_content = "Request host:$host port:$port https:$https\n\n".$request.$request_content;
$b64_out = encode_base64($request_content);

# Send it back to php
print $b64_out;


##############################################
sub socket_ops	{

	my $string = shift;
	$protocol = "tcp";
	my $content = "";
	my ($socket,$client_socket);
	# HTTPS
	if($https)	{
		if( $socket = IO::Socket::SSL->new(
			PeerAddr        => "$host",
			PeerPort        => "$port",
			SSL_verify_mode => 0x00,
		) )	{
			# Send it
			print $socket $string;

			# Get the response
 			my @lines = $socket->getlines();
			foreach my $line (@lines) {
				$content .= $line;
			}
		
			$socket->autoflush;
			if( $socket->close(
			    SSL_no_shutdown => 1, 
			    SSL_ctx_free    => 1,    
			) )	{
			}
			else	{
				# "not ok: $SSL_ERROR";
			}
		}
		else	{
			# socket failed
			$content .= "failed to connect SSL, host:$host port:$port  $SSL_ERROR\n";
		}
	}


	# HTTP
	else	{
		if ($socket = new IO::Socket::INET (
			PeerHost => "$host",
			PeerPort => "$port",
			Proto => "$protocol",
			Timeout => 1
		)) {
			# Send it
			print $socket $string;

			# Get the response
 	 		my @lines = $socket->getlines();
			foreach my $line (@lines) {
				$content .= $line;
			}
			$socket->close();
		}
		else	{
			$content .= "failed to connect, host:$host port:$port\n";
		}
	}
	return $content;
}

##############################################

sub trim {

	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;

}
