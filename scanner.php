<META NAME="robots" CONTENT="noindex,nofollow">

<?php
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scan_dir'])) {
    $scanDir = realpath($_POST['scan_dir']);
    if ($scanDir === false || !is_dir($scanDir)) {
        die('Geçersiz dizin yolu!');
    }
    $scanner = new ShellScanner();
    $scanner->scan($scanDir);
    exit;
}

class ShellScanner {
    private $suspiciousFiles = [
        '.php', '.phtml', '.php3', '.php4', '.php5', '.php7', '.pht', '.phps',
        '.cgi', '.pl', '.py', '.jsp', '.asp', '.aspx', '.sh', '.bash'
    ];

    private $suspiciousNames = [
        'shell', 'backdoor', 'hack', 'security', 'sec', 'injection', 'wso',
        'cmd', 'root', 'upload', 'webadmin', 'admin', 'alfa', 'c99', 'r57',
        'b374k', 'c100', 'marijuana', 'predator', 'sad', 'spy', 'worm', 'dra'
    ];

    private $patterns = [
        'eval\s*\(' => 'Eval kullanımı',
        'base64_decode' => 'Base64 kod',
        'system\s*\(' => 'Sistem komutu',
        'exec\s*\(' => 'Exec komutu',
        'shell_exec' => 'Shell komutu',
        'passthru' => 'Passthru kullanımı',
        '\$_POST\s*\[.*\]\s*\(' => 'POST ile kod çalıştırma',
        '\$_GET\s*\[.*\]\s*\(' => 'GET ile kod çalıştırma',
        'move_uploaded_file' => 'Dosya yükleme',
        'file_get_contents' => 'Dosya okuma',
        'file_put_contents' => 'Dosya yazma',
        'str_rot13' => 'ROT13 şifreleme',
        'gzinflate' => 'GZIP çözme',
        'gzuncompress' => 'GZIP çözme',
        'error_reporting\(0\)' => 'Hata gizleme'
    ];

    private $count = 0;
    private $threats = 0;
    private $startTime;
    private $foundThreats = []; 

    private function showHeader() {
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Web Shell Tarayıcı</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; background: #f0f2f5; }
                .container { max-width: 1200px; margin: 0 auto; }
                .header { background: #1a237e; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
                .progress { background: #fff; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .threat { background: #ff5252; color: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; transition: all 0.3s ease; }
                .threat:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
                .threat-high { background: #d32f2f; }
                .threat-medium { background: #f44336; }
                .threat-low { background: #ff5252; }
                .threat-info { background: #fff; padding: 10px; border-radius: 4px; margin-top: 10px; color: #333; }
                .stats { display: flex; justify-content: space-between; margin-bottom: 20px; }
                .stat-box { background: #fff; padding: 15px; border-radius: 8px; flex: 1; margin: 0 10px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .footer { background: #1a237e; color: white; padding: 20px; border-radius: 8px; margin-top: 20px; }
                .matches { background: #ffebee; padding: 10px; border-radius: 4px; margin-top: 10px; }
                .match-item { color: #c62828; margin: 5px 0; }
                .input-form { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .input-field { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
                .submit-btn { background: #1a237e; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
                .submit-btn:hover { background: #283593; }
                .threat-count { font-size: 1.2em; font-weight: bold; background: #fff; color: #d32f2f; padding: 3px 8px; border-radius: 12px; margin-left: 10px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔍 Web Shell Tarayıcı</h1>
                    <p>Başlangıç zamanı: ' . date('Y-m-d H:i:s') . '</p>
                </div>';
    }

    public function showStartForm() {
        $this->showHeader();
        echo '<div class="input-form">
                <h2>Tarama Başlat</h2>
                <form method="post">
                    <label for="scan_dir">Taranacak Dizin:</label>
                    <input type="text" name="scan_dir" id="scan_dir" class="input-field" 
                           value="' . htmlspecialchars(getcwd()) . '" required>
                    <button type="submit" class="submit-btn">Taramayı Başlat</button>
                </form>
            </div>
            </div></body></html>';
    }

    public function scan($dir) {
        $this->startTime = microtime(true);
        $this->showHeader();
        echo '<div class="progress" id="progress">Tarama başlatılıyor...</div>';
        $this->scanDir($dir);
        $this->showThreats(); 
        $this->showFooter();
    }

    private function scanDir($dir) {
        if (!is_readable($dir)) return;
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;
            
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($path)) {
                $this->scanDir($path);
            } else {
                $this->checkFile($path, $file);
                $this->updateProgress($path);
            }
        }
    }

    private function checkFile($path, $filename) {
        $this->count++;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array('.' . $ext, $this->suspiciousFiles)) {
            return;
        }

        $matches = [];
        
        foreach ($this->suspiciousNames as $name) {
            if (stripos($filename, $name) !== false) {
                $matches[] = "📁 Şüpheli dosya adı: $name";
            }
        }

        if (is_readable($path)) {
            $content = file_get_contents($path);
            if ($content !== false) {
                foreach ($this->patterns as $pattern => $desc) {
                    if (preg_match("/$pattern/i", $content)) {
                        $matches[] = "⚠️ " . $desc;
                    }
                }
            }
        }

        if (!empty($matches)) {
            $this->foundThreats[] = [
                'path' => $path,
                'matches' => $matches,
                'count' => count($matches),
                'size' => filesize($path),
                'mtime' => filemtime($path),
                'perms' => fileperms($path)
            ];
            $this->threats++;
        }
    }

    private function showThreats() {
        if (empty($this->foundThreats)) {
            echo '<div class="input-form"><p>Hiç tehdit bulunamadı.</p></div>';
            return;
        }

        usort($this->foundThreats, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        foreach ($this->foundThreats as $threat) {
            $threatClass = $threat['count'] >= 3 ? 'threat-high' : 
                          ($threat['count'] == 2 ? 'threat-medium' : 'threat-low');
            
            echo '<div class="threat ' . $threatClass . '">
                <h3>⚠️ Şüpheli Dosya Tespit Edildi! 
                    <span class="threat-count">' . $threat['count'] . ' tehdit</span></h3>
                <div class="threat-info">
                    <p><strong>Dosya:</strong> ' . htmlspecialchars($threat['path']) . '</p>
                    <p><strong>Boyut:</strong> ' . $this->formatSize($threat['size']) . '</p>
                    <p><strong>Son değişiklik:</strong> ' . date("Y-m-d H:i:s", $threat['mtime']) . '</p>
                    <p><strong>İzinler:</strong> ' . substr(sprintf('%o', $threat['perms']), -4) . '</p>
                    <div class="matches">
                        <h4>Tespit Edilen Tehditler:</h4>';
            foreach ($threat['matches'] as $match) {
                echo '<div class="match-item">• ' . htmlspecialchars($match) . '</div>';
            }
            echo '</div></div></div>';
        }
    }

    private function updateProgress($currentFile) {
        echo '<script>
            document.getElementById("progress").innerHTML = "Taranan dosya: ' . $this->count . 
            '<br>Bulunan tehdit: ' . $this->threats . 
            '<br>Şu an taranan: ' . htmlspecialchars(basename($currentFile)) . 
            '<br>Geçen süre: ' . $this->getElapsedTime() . '";
        </script>';
        flush();
    }

    private function showFooter() {
        echo '<div class="footer">
                <h2>Tarama Tamamlandı!</h2>
                <div class="stats">
                    <div class="stat-box">
                        <h3>Taranan Dosya</h3>
                        <p>' . $this->count . '</p>
                    </div>
                    <div class="stat-box">
                        <h3>Bulunan Tehdit</h3>
                        <p>' . $this->threats . '</p>
                    </div>
                    <div class="stat-box">
                        <h3>Toplam Süre</h3>
                        <p>' . $this->getElapsedTime() . '</p>
                    </div>
                </div>
                <br>
                <form method="post">
                    <button type="submit" class="submit-btn">Yeni Tarama Başlat</button>
                </form>
            </div>
        </div></body></html>';
    }

    private function formatSize($size) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(($size ? log($size) : 0) / log(1024));
        return sprintf("%.2f %s", $size / pow(1024, $power), $units[$power]);
    }

    private function getElapsedTime() {
        $elapsed = microtime(true) - $this->startTime;
        $hours = floor($elapsed / 3600);
        $minutes = floor(($elapsed % 3600) / 60);
        $seconds = floor($elapsed % 60);
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['scan_dir'])) {
    $scanner = new ShellScanner();
    $scanner->showStartForm();
}
?>
