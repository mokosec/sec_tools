## Infosec related tools

### php_curl.php

A lightweight php page using php_curl to make web requests and display the headers, content, and SSL certificate information.
Used mainly in trouble shooting and testing new sites. SSL certification checks are off for maximum visibility.

### php_converters.php

Yet another converter page. A collection of converters I find useful.

Some new ones that are less prevalent:
1. Convert Powershell style base64 directly to a readable version
2. Convert Base64 encoded gzip and zlib to a readable version
3. Custom alphabet Base64 decoder

### php_pushit.php and pushit.pl

Basically a packet generator for complex text payloads over HTTP and HTTPS TCP with selectable port.
Used for more complex testing like pushing malware POST data to C2s. Use appropriate opsec!

1. Grab a single request from a pcap and just send the whole thing
2. Great for testing your security stack and freaking out defenders
3. Modify the host and port
4. Uses Perl sockets and ssl sockets and a simple php front end
5. If the request does not follow the standard expect long timeouts




