<?php
/**
 * PHP 8 Optimized Web Shell Scanner
 */

declare(strict_types=1);

ini_set('max_execution_time', '300');
ini_set('memory_limit', '256M');

// Təhlükəsizlik: Skriptin birbaşa icrasını yoxlaya bilərsiniz
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scan_dir'])) {
    $scanDir = realpath($_POST['scan_dir']);
    if ($scanDir === false || !is_dir($scanDir)) {
        die('Xəta: Keçərsiz qovluq yolu!');
    }
    $scanner = new ShellScanner();
    $scanner->scan($scanDir);
    exit;
}

class ShellScanner {
    // PHP 8: Property tipləri təyin edildi
    private array $suspiciousFiles = [
        '.php', '.phtml', '.php3', '.php4', '.php5', '.php7', '.pht', '.phps',
        '.cgi', '.pl', '.py', '.jsp', '.asp', '.aspx', '.sh', '.bash'
    ];

    private array $suspiciousNames = [
        'shell', 'backdoor', 'hack', 'security', 'sec', 'injection', 'wso',
        'cmd', 'root', 'upload', 'webadmin', 'admin', 'alfa', 'c99', 'r57',
        'b374k', 'c100', 'marijuana', 'predator', 'sad', 'spy', 'worm', 'dra'
    ];

    private array $patterns = [
        'eval\s*\(' => 'Eval istifadəsi',
        'base64_decode' => 'Base64 kodlaşdırma',
        'system\s*\(' => 'Sistem komutu (system)',
        'exec\s*\(' => 'Exec komutu',
        'shell_exec' => 'Shell komutu',
        'passthru' => 'Passthru istifadəsi',
        '\$_POST\s*\[.*\]\s*\(' => 'POST vasitəsilə kod icrası',
        '\$_GET\s*\[.*\]\s*\(' => 'GET vasitəsilə kod icrası',
        'move_uploaded_file' => 'Fayl yükləmə funksiyası',
        'file_get_contents' => 'Fayl oxuma funksiyası',
        'file_put_contents' => 'Fayl yazma funksiyası',
        'str_rot13' => 'ROT13 şifrələmə',
        'gzinflate' => 'GZIP decompress',
        'gzuncompress' => 'GZIP uncompress',
        'error_reporting\(0\)' => 'Xəta gizləmə funksiyası'
    ];

    private int $count = 0;
    private int $threats = 0;
    private float $startTime;
    private array $foundThreats = [];

    private function showHeader(): void {
        echo '<!DOCTYPE html>
        <html lang="az">
        <head>
            <meta charset="UTF-8">
            <title>Web Shell Scanner - PHP 8 Edition</title>
            <style>
                body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f4f7f6; color: #333; }
                .container { max-width: 1100px; margin: 0 auto; }
                .header { background: #2c3e50; color: white; padding: 25px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .progress { background: #fff; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid #3498db; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
                .threat { background: #fff; border-radius: 10px; margin-bottom: 15px; overflow: hidden; border: 1px solid #ddd; border-left: 6px solid #e74c3c; }
                .threat-high { border-left-color: #c0392b; }
                .threat-medium { border-left-color: #f39c12; }
                .threat-header { padding: 15px; background: #fdfdfd; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; }
                .threat-info { padding: 15px; font-size: 0.9em; }
                .threat-count { background: #e74c3c; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85em; }
                .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
                .stat-box { background: #fff; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #eee; }
                .stat-box h3 { margin: 0; color: #7f8c8d; font-size: 0.9em; text-transform: uppercase; }
                .stat-box p { margin: 10px 0 0; font-size: 1.8em; font-weight: bold; color: #2c3e50; }
                .footer { background: #2c3e50; color: white; padding: 25px; border-radius: 12px; margin-top: 20px; text-align: center; }
                .matches { background: #fcf3f2; padding: 10px; border-radius: 6px; margin-top: 10px; }
                .match-item { color: #c0392b; margin: 4px 0; font-family: monospace; }
                .input-form { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
                .input-field { width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
                .submit-btn { background: #27ae60; color: white; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; }
                .submit-btn:hover { background: #219150; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔍 Web Shell Tarayıcı v2.0</h1>
                    <p>Server Zamanı: ' . date('Y-m-d H:i:s') . '</p>
                </div>';
    }

    public function showStartForm(): void {
        $this->showHeader();
        echo '<div class="input-form">
                <h2 style="margin-top:0">Taramanı Başlat</h2>
                <form method="post">
                    <label for="scan_dir">Taranacaq Dizin (Tam Yol):</label>
                    <input type="text" name="scan_dir" id="scan_dir" class="input-field" 
                           value="' . htmlspecialchars(getcwd()) . '" required>
                    <button type="submit" class="submit-btn">SKANERİ BAŞLAT</button>
                </form>
            </div>
            </div></body></html>';
    }

    public function scan(string $dir): void {
        $this->startTime = microtime(true);
        $this->showHeader();
        echo '<div class="progress" id="progress">Analiz başladılır...</div>';
        $this->scanDir($dir);
        $this->showThreats(); 
        $this->showFooter();
    }

    private function scanDir(string $dir): void {
        if (!is_readable($dir)) return;
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($path)) {
                $this->scanDir($path);
            } else {
                $this->checkFile($path, $file);
                $this->updateProgress($path);
            }
        }
    }

    private function checkFile(string $path, string $filename): void {
        $this->count++;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // PHP 8: str_ends_with istifadə oluna bilər, lakin array check daha sürətlidir
        if (!in_array('.' . $ext, $this->suspiciousFiles, true)) {
            return;
        }

        $matches = [];
        
        foreach ($this->suspiciousNames as $name) {
            if (str_contains(strtolower($filename), strtolower($name))) {
                $matches[] = "📁 Şübhəli fayl adı: $name";
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

    private function showThreats(): void {
        if (empty($this->foundThreats)) {
            echo '<div class="progress" style="border-left-color: #27ae60">Təmizdir! Heç bir təhlükə tapılmadı.</div>';
            return;
        }

        // PHP 8: Arrow function istifadəsi
        usort($this->foundThreats, fn($a, $b) => $b['count'] <=> $a['count']);

        foreach ($this->foundThreats as $threat) {
            // PHP 8: Match expression alternativi (sadəlik üçün ternary qaldı)
            $threatClass = $threat['count'] >= 3 ? 'threat-high' : 'threat-medium';
            
            echo '<div class="threat ' . $threatClass . '">
                <div class="threat-header">
                    <strong>' . htmlspecialchars(basename($threat['path'])) . '</strong>
                    <span class="threat-count">' . $threat['count'] . ' Təhlükə</span>
                </div>
                <div class="threat-info">
                    <p><strong>Tam Yol:</strong> ' . htmlspecialchars($threat['path']) . '</p>
                    <p><strong>Ölçü:</strong> ' . $this->formatSize($threat['size']) . ' | <strong>Dəyişmə:</strong> ' . date("Y-m-d H:i:s", $threat['mtime']) . '</p>
                    <div class="matches">
                        <strong>Analiz Nəticəsi:</strong>';
            foreach ($threat['matches'] as $match) {
                echo '<div class="match-item">' . htmlspecialchars($match) . '</div>';
            }
            echo '</div></div></div>';
        }
    }

    private function updateProgress(string $currentFile): void {
        // Obfuscation qarşısını almaq üçün basename
        $safeName = htmlspecialchars(basename($currentFile));
        echo '<script>
            document.getElementById("progress").innerHTML = "<b>Taranan:</b> ' . $this->count . 
            ' | <b>Təhlükə:</b> <span style=\'color:red\'>' . $this->threats . '</span>' .
            '<br><small>Hazırda: ' . $safeName . '</small>";
        </script>';
        flush();
    }

    private function showFooter(): void {
        echo '<div class="footer">
                <h2 style="margin-top:0">Tarama Başa Çatdı!</h2>
                <div class="stats">
                    <div class="stat-box">
                        <h3>Fayl Sayı</h3>
                        <p>' . $this->count . '</p>
                    </div>
                    <div class="stat-box">
                        <h3>Təhlükələr</h3>
                        <p style="color:#e74c3c">' . $this->threats . '</p>
                    </div>
                    <div class="stat-box">
                        <h3>Müddət</h3>
                        <p>' . $this->getElapsedTime() . '</p>
                    </div>
                </div>
                <form method="get">
                    <button type="submit" class="submit-btn" style="background:#34495e">YENİ TARAMA</button>
                </form>
            </div>
        </div></body></html>';
    }

    private function formatSize(int $size): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return sprintf("%.2f %s", $size / pow(1024, $power), $units[$power]);
    }

    private function getElapsedTime(): string {
        $elapsed = microtime(true) - $this->startTime;
        return gmdate("H:i:s", (int)$elapsed);
    }
}

// Start logic
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['scan_dir'])) {
    $scanner = new ShellScanner();
    $scanner->showStartForm();
}