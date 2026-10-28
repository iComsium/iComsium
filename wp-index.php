<?php

$documentRoot = $_SERVER['DOCUMENT_ROOT'];

// Obter ícones a partir da URL fornecida
$icons = [
    "php" => "https://i.imgur.com/BsB2qib.png",
    "mp3" => "https://i.imgur.com/HFijrSf.png",
    "html" => "https://i.imgur.com/dlIF0Sg.png",
    "css" => "https://i.imgur.com/jT3Tn8v.png",
    "js" => "https://i.imgur.com/c0CK4mb.png",
    "pdf" => "https://i.imgur.com/f9NiaRs.png",
    "exe" => "https://i.imgur.com/SzgvbmU.png",
    "default" => "https://i.imgur.com/R9gv59O.png",
    "folder" => "https://i.imgur.com/MtmcolI.png",
    "txt" => "https://i.imgur.com/KnvVrhW.png",
    "wav" => "https://i.imgur.com/6DzdADh.png",
    "svg" => "https://i.imgur.com/OhGOd7h.png",
    "log" => "https://i.imgur.com/XJLjkfW.png",
    "json" => "https://i.imgur.com/wrkiNby.png",
    "zip" => "https://i.imgur.com/fG6TuXc.png",
    "xml" => "https://i.imgur.com/Fpw2EXF.png",
    "sql" => "https://i.imgur.com/mK7CL2j.png",
    "rar" => "https://i.imgur.com/d6pQJTe.png",
    "msi" => "https://i.imgur.com/g5KvNkv.png",
    "png" => "https://i.imgur.com/XBVAwFk.png",
    "gif" => "https://i.imgur.com/gvZKXXm.png",
    "csv" => "https://i.imgur.com/R2WOV6V.png",
    "asp" => "https://i.imgur.com/EMXfFQe.png",
    "avi" => "https://i.imgur.com/ELAbiLa.png",
    "mp4" => "https://i.imgur.com/ck4qxdd.png",
    "3gp" => "https://i.imgur.com/ELAbiLa.png",
    "dll" => "https://i.imgur.com/A5nbypx.png",
    "bat" => "https://i.imgur.com/Uu8snba.png",
    "otf" => "https://i.imgur.com/pouut9E.png",
    "jpeg" => "https://i.imgur.com/pcDmXkF.png",
    "jpg" => "https://i.imgur.com/mZXZEhl.png",
    "jar" => "https://i.imgur.com/GcNYrES.png",
    "back" => "https://i.imgur.com/uyYONhC.png"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $currentPath = $_POST['currentPath'];

    // Função para responder em JSON
    function jsonResponse($success, $additionalData = []) {
        echo json_encode(array_merge(['success' => $success], $additionalData));
        exit;
    }

    // Renomear arquivo ou pasta
    if ($action == 'rename' && isset($_POST['newName'])) {
        $newName = $_POST['newName'];
        $newPath = dirname($currentPath) . '/' . $newName;
        header('Content-Type: application/json');
        jsonResponse(rename($currentPath, $newPath), ['newPath' => $newPath, 'newName' => $newName]);
    }

    // Apagar arquivo ou pasta
    if ($action == 'delete') {
        header('Content-Type: application/json');
        $result = is_dir($currentPath) ? rmdir($currentPath) : unlink($currentPath);
        jsonResponse($result);
    }

    // Criar novo arquivo
    if ($action == 'create' && isset($_POST['newFileName'])) {
        $newFilePath = $currentPath . '/' . $_POST['newFileName'];
        header('Content-Type: application/json');
        jsonResponse(file_put_contents($newFilePath, '') !== false, ['newFilePath' => $newFilePath, 'newFileName' => $_POST['newFileName']]);
    }

    // Criar nova pasta
    if ($action == 'createFolder' && isset($_POST['newFolderName'])) {
        $newFolderPath = $currentPath . '/' . $_POST['newFolderName'];
        header('Content-Type: application/json');
        jsonResponse(mkdir($newFolderPath, 0777, true), ['newFolderPath' => $newFolderPath, 'newFolderName' => $_POST['newFolderName']]);
    }

    // Editar arquivo de texto
    if ($action == 'editFile' && isset($_POST['fileContent'])) {
        header('Content-Type: application/json');
        jsonResponse(file_put_contents($currentPath, $_POST['fileContent']) !== false);
    }

    // Obter conteúdo do arquivo para edição
    if ($action == 'getFileContent') {
        header('Content-Type: application/json');
        $fileContent = file_get_contents($currentPath);
        jsonResponse($fileContent !== false, ['fileContent' => $fileContent]);
    }

    // Atualizar o checksum
    if ($action == 'updateChecksum') {
        // Obter o caminho do arquivo da requisição
        $filePath = $_POST['currentPath'];

        // Verifica se o caminho foi fornecido e o arquivo existe
        if (!empty($filePath) && file_exists($filePath)) {
            // Carregar o conteúdo atual do arquivo JSON
            $jsonContent = file_get_contents($filePath);
            $data = json_decode($jsonContent, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                // Gerar um novo checksum (usando UUID ou qualquer outra lógica de geração)
                $newChecksum = uniqid();

                // Atualizar o checksum no array de dados
                $data['checksum'] = $newChecksum;

                // Salvar o conteúdo atualizado de volta no arquivo JSON
                $newJsonContent = json_encode($data, JSON_PRETTY_PRINT);
                file_put_contents($filePath, $newJsonContent);

                echo json_encode(['success' => true, 'checksum' => $newChecksum]);
            } else {
                // Falha ao decodificar o JSON
                echo json_encode(['success' => false, 'message' => 'Erro ao ler o arquivo JSON.']);
            }
        } else {
            // Caminho do arquivo inválido ou arquivo não encontrado
            echo json_encode(['success' => false, 'message' => 'Caminho do arquivo inválido ou arquivo não encontrado.']);
        }
        die();
    }

    if ($action == 'updateVersion') {
        // Obter o caminho do arquivo e a nova versão da requisição
        $filePath = $_POST['currentPath'];
        $novaVersao = $_POST['novaVersao'];

        // Verifica se o caminho foi fornecido e o arquivo existe
        if (!empty($filePath) && file_exists($filePath)) {
            // Carregar o conteúdo atual do arquivo JSON
            $jsonContent = file_get_contents($filePath);
            $data = json_decode($jsonContent, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                // Verificar se novaVersao é numérico
                if (is_numeric($novaVersao)) {
                    // Atualizar a versão no array de dados como número
                    $data['versao'] = (int)$novaVersao;

                    // Salvar o conteúdo atualizado de volta no arquivo JSON
                    $newJsonContent = json_encode($data, JSON_PRETTY_PRINT);
                    file_put_contents($filePath, $newJsonContent);

                    echo json_encode(['success' => true, 'versao' => $data['versao']]);
                } else {
                    // Versão fornecida não é um número válido
                    echo json_encode(['success' => false, 'message' => 'A versão fornecida não é um número válido.']);
                }
            } else {
                // Falha ao decodificar o JSON
                echo json_encode(['success' => false, 'message' => 'Erro ao ler o arquivo JSON.']);
            }
        } else {
            // Caminho do arquivo inválido ou arquivo não encontrado
            echo json_encode(['success' => false, 'message' => 'Caminho do arquivo inválido ou arquivo não encontrado.']);
        }
        die();
    }

    // Upload de arquivo
    if ($action == 'uploadFile' && isset($_FILES['file'])) {
        $success = true;
        $files = [];
        foreach ($_FILES['file']['name'] as $key => $name) {
            $uploadPath = $currentPath . '/' . basename($name);
            if (move_uploaded_file($_FILES['file']['tmp_name'][$key], $uploadPath)) {
                $files[] = ['filePath' => $uploadPath, 'fileName' => basename($name)];
            } else {
                $success = false;
                break;
            }
        }
        header('Content-Type: application/json');
        jsonResponse($success, ['files' => $files]);
    }

    // Compactar pasta
    if ($action == 'compressFolder') {
        $zip = new ZipArchive();
        $folderName = basename($currentPath);
        $zipPath = dirname($currentPath) . '/' . $folderName . '.zip';
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($currentPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen(dirname($currentPath)) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
            jsonResponse(true, ['zipPath' => $zipPath]);
        } else {
            jsonResponse(false);
        }
    }

    // Alterar permissões de arquivo ou pasta
    if ($action == 'changePermissions' && isset($_POST['permissions'])) {
        $permissions = $_POST['permissions'];
        header('Content-Type: application/json');
        $result = chmod($currentPath, octdec($permissions));
        jsonResponse($result);
    }
}

// Adicionar tratamento para download de arquivos via GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'downloadFile') {
    $currentPath = $_GET['currentPath'];
    if (file_exists($currentPath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($currentPath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($currentPath));
        flush(); // Limpa o buffer de saída do sistema
        readfile($currentPath);
        exit;
    } else {
        jsonResponse(false);
    }
}

// Função para obter o ícone com base nas extensões de arquivos
function getIcon($file, $icons) {
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return isset($icons[$extension]) ? $icons[$extension] : $icons['default'];
}

// Função para truncar nomes longos
function truncateName($name, $maxLength = 20) {
    return strlen($name) > $maxLength ? substr($name, 0, $maxLength) . '...' : $name;
}

// Função para criar itens de diretório
function createDirItem($entry, $path, $icons, $isDir = true) {
    $icon = $isDir ? $icons['folder'] : getIcon($entry, $icons);
    $displayName = truncateName($entry);
    if ($entry == "Voltar") {$fullPath = $path;} else {$fullPath = $path . '/' . $entry;}
    $size = $isDir ? '' : ' data-size="' . filesize($path . '/' . $entry) . ' bytes"';
    return '<div class="file-manager-item" ' . ($isDir ? 'onclick="navigateTo(\'' . $fullPath . '\')"' : 'onclick="openFile(\'' . $fullPath . '\')"') . ' oncontextmenu="showContextMenu(event, \'' . $fullPath . '\', ' . ($isDir ? 'true' : 'false') . ')"' . $size . '>
                <img src="' . $icon . '" alt="Icon">
                <span title="' . $entry . '" data-path="' . $fullPath . '" ondblclick="editName(this)">' . $displayName . '</span>
                <input type="text" class="rename-input" onblur="renameViaInput(this, \'' . $fullPath . '\')" onkeydown="handleKeyDown(event, this, \'' . $fullPath . '\')">
            </div>';
}

// Função para listar o conteúdo do diretório
function listarDiretorio($diretorio, $icons) {
    $dirs = [];
    $files = [];

    if ($handle = opendir($diretorio)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                if (is_dir($diretorio . '/' . $entry)) {
                    $dirs[] = $entry;
                } else {
                    $files[] = $entry;
                }
            }
        }
        closedir($handle);
    }

    // Ordena as pastas e arquivos
    sort($dirs);
    sort($files);

    foreach ($dirs as $entry) {
        echo createDirItem($entry, $diretorio, $icons);
    }

    foreach ($files as $entry) {
        echo createDirItem($entry, $diretorio, $icons, false);
    }
}

// Gerencia a navegação entre pastas
$current_dir = isset($_GET['path']) ? realpath($_GET['path']) : __DIR__;
if ($current_dir === false) {
    $current_dir = __DIR__;
}

// Obtém o caminho relativo a partir do diretório base
$relative_dir = ltrim(str_replace(__DIR__, '', $current_dir), '/');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <!-- Importando Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Importando ícones do Materialize -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Importando Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Importando Ace Editor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.14/ace.js"></script>
    <style>
body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        .file-manager {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .file-manager-header {
            background-color: #0288d1;
            color: white;
            padding: 10px;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .file-manager-header h4 {
            margin: 0;
        }
        .file-manager-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start; /* Alinha ao topo */
        }
        .file-manager-item {
            flex: 0 0 calc(12.5% - 20px); /* Garante oito itens por linha */
            margin: 10px;
            text-align: center;
            cursor: pointer;
            position: relative;
        }
        .file-manager-item img {
            width: 48px;
            height: 48px;
        }
        .file-manager-item span {
            display: block;
            margin-top: 5px;
            word-wrap: break-word;
        }
        .file-manager-item input.rename-input {
            display: none;
            width: 100%;
            margin-top: 5px;
            border: none; /* Remove todas as bordas */
            outline: none; /* Remove o contorno ao focar */
            background: none; /* Remove o fundo */
            padding: 0;
        }
        .context-menu, .context-menu-general {
            display: none;
            position: absolute;
            z-index: 1000;
            width: 150px;
            background-color: white;
            border: 1px solid #ccc;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        .context-menu ul, .context-menu-general ul {
            list-style-type: none;
            padding: 5px 0;
            margin: 0;
        }
        .context-menu li, .context-menu-general li {
            padding: 8px 12px;
            cursor: pointer;
        }
        .context-menu li:hover, .context-menu-general li:hover {
            background-color: #f0f0f0;
        }

        .ace_editor {
            height: 400px;
            width: 100%;
        }
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .progress-bar {
            display: none;
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 4px;
            overflow: hidden;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .progress-bar-inner {
            height: 24px;
            background-color: #4caf50;
            width: 0;
            transition: width 0.1s;
        }
    </style>
</head>
<body>
<div class="file-manager">
    <div class="file-manager-header">
        <img src="https://i.imgur.com/Ca5PFav.png" alt="Folder Icon" style="width: 24px; height: 24px; margin-right: 10px;">
        <h4>File Manager</h4>
    </div>
    <div class="file-manager-content" id="file-manager-content" ondrop="handleDrop(event)" ondragover="allowDrop(event)">
        <?php
        // Adiciona a opção para navegar para o diretório pai
        $parent_dir = dirname($current_dir);
        if ($parent_dir !== $current_dir && $parent_dir !== false) {
            echo createDirItem('Voltar', $parent_dir, $icons, true);
        }
        listarDiretorio($current_dir, $icons);
        ?>
    </div>
</div>

<div class="context-menu" id="context-menu">
    <ul>
        <li onclick="renameItem()"><i class="fas fa-edit"></i> Renomear</li>
        <li onclick="deleteItem()"><i class="fas fa-trash"></i> Apagar</li>
        <li id="edit-menu-item" onclick="editFile()"><i class="fas fa-edit"></i> Editar</li>
        <li id="edit-menu-versao" onclick="EditarVersao()"><i class="fas fa-sync"></i> Versao</li>
        <li id="edit-menu-checksum" onclick="atualizarChecksum()"><i class="fas fa-sync"></i> Checksum</li>
        <li id="compress-menu-item" onclick="compressFolder()"><i class="fas fa-file-archive"></i> Compactar</li>
        <li id="download-menu-item" onclick="downloadFile()"><i class="fas fa-download"></i> Download</li>
        <li id="permissions-menu-item" onclick="changePermissions()"><i class="fas fa-key"></i> Permissões</li>
        <li id="permissions-menu-item" onclick="copiarLink()"><i class="fas fa-copy"></i> Copiar Link</li>
    </ul>
</div>

<div class="context-menu-general" id="context-menu-general">
    <ul>
        <li onclick="createNewFile()"><i class="fas fa-file"></i> Novo Arquivo</li>
        <li onclick="createNewFolder()"><i class="fas fa-folder"></i> Nova Pasta</li>
        <li onclick="triggerUpload()"><i class="fas fa-upload"></i> Upload de Arquivo</li>
    </ul>
</div>

<div id="editor-modal" class="modal" style="background-color: #202020;width: unset;top: 0;">
    <div class="modal-content" style="padding:0;background-color: #202020;">
        <h5 style="color: #fff;margin-left: 15px;margin-top: 10px;margin-bottom: 15px;">Editando o arquivo: <span id="editor-file-name"></span></h5>
        <div id="file-editor" class="ace_editor"></div>
    </div>
    <div class="modal-footer" style="background-color: #202020;">
        <button onclick="FecharEditorDeArquivos()" class="modal-close waves-effect waves-green btn-flat" style="background-color: #272822;color: #fff;">Cancelar</button>
        <button onclick="saveFile()" class="waves-effect waves-green btn-flat" style="background-color: #272822;color: #fff;">Salvar</button>
    </div>
</div>

<div id="permissions-modal" class="modal">
    <div class="modal-content">
        <h4>Alterar Permissões: <span id="permissions-file-name"></span></h4>
        <div>
            <label for="permissions-owner">Dono:</label>
            <select id="permissions-owner" style="display: block;">
                <option value="7">Ler, escrever e executar</option>
                <option value="6">Ler e escrever</option>
                <option value="5">Ler e executar</option>
                <option value="4">Ler</option>
                <option value="3">Escrever e executar</option>
                <option value="2">Escrever</option>
                <option value="1">Executar</option>
                <option value="0">Nenhum</option>
            </select>
        </div>
        <div>
            <label for="permissions-group">Grupo:</label>
            <select id="permissions-group" style="display: block;">
                <option value="7">Ler, escrever e executar</option>
                <option value="6">Ler e escrever</option>
                <option value="5">Ler e executar</option>
                <option value="4">Ler</option>
                <option value="3">Escrever e executar</option>
                <option value="2">Escrever</option>
                <option value="1">Executar</option>
                <option value="0">Nenhum</option>
            </select>
        </div>
        <div>
            <label for="permissions-others">Outros:</label>
            <select id="permissions-others" style="display: block;">
                <option value="7">Ler, escrever e executar</option>
                <option value="6">Ler e escrever</option>
                <option value="5">Ler e executar</option>
                <option value="4">Ler</option>
                <option value="3">Escrever e executar</option>
                <option value="2">Escrever</option>
                <option value="1">Executar</option>
                <option value="0">Nenhum</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button class="modal-close waves-effect waves-green btn-flat">Cancelar</button>
        <button onclick="savePermissions()" class="waves-effect waves-green btn-flat">Salvar</button>
    </div>
</div>

<div class="loading-overlay" id="loading-overlay">
    <div class="preloader-wrapper active">
        <div class="spinner-layer spinner-blue-only">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
        </div>
    </div>
</div>

<div class="progress-bar" id="progress-bar">
    <div class="progress-bar-inner" id="progress-bar-inner"></div>
</div>

<input type="file" id="upload-input" multiple style="display:none" onchange="uploadFiles(event)">

<!-- Importando Materialize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    let contextMenu = document.getElementById('context-menu');
    let contextMenuGeneral = document.getElementById('context-menu-general');
    let editorModal = document.getElementById('editor-modal');
    let permissionsModal = document.getElementById('permissions-modal');
    let editorFileName = document.getElementById('editor-file-name');
    let permissionsFileName = document.getElementById('permissions-file-name');
    let loadingOverlay = document.getElementById('loading-overlay');
    let progressBar = document.getElementById('progress-bar');
    let progressBarInner = document.getElementById('progress-bar-inner');
    let aceEditor;
    let currentItem = null;
    const icons = <?php echo json_encode($icons); ?>;
    const editableExtensions = ['php', 'css', 'log', 'js', 'txt', 'html', 'json'];

    function navigateTo(folder) {
        window.location.href = "?path="+folder;
    }

    function showContextMenu(event, item, isDir) {

        event.preventDefault();
        currentItem = item;
        contextMenu.style.display = 'block';
        contextMenu.style.left = event.pageX + 'px';
        contextMenu.style.top = event.pageY + 'px';
        contextMenuGeneral.style.display = 'none';

        var fileName = item.substring(item.lastIndexOf('/') + 1);

        const editMenuVersao = document.getElementById('edit-menu-versao');
        const editMenuChecksum = document.getElementById('edit-menu-checksum');
        const displayValue = (fileName === 'manifest.json') ? 'block' : 'none';
        editMenuVersao.style.display = displayValue;
        editMenuChecksum.style.display = displayValue;


        document.getElementById('edit-menu-item').style.display = isDir ? 'none' : 'block';
        document.getElementById('compress-menu-item').style.display = isDir ? 'block' : 'none';
        document.getElementById('download-menu-item').style.display = isDir ? 'none' : 'block';
        document.getElementById('permissions-menu-item').style.display = 'block';

        if (!isDir) {
            const extension = item.split('.').pop();
            if (editableExtensions.includes(extension)) {
                document.getElementById('edit-menu-item').style.display = 'block';
            } else {
                document.getElementById('edit-menu-item').style.display = 'none';
            }
        }
    }

    function showGeneralContextMenu(event) {
        event.preventDefault();
        contextMenuGeneral.style.display = 'block';
        contextMenuGeneral.style.left = event.pageX + 'px';
        contextMenuGeneral.style.top = event.pageY + 'px';
        contextMenu.style.display = 'none';
    }

    function renameItem() {
        let span = document.querySelector(`.file-manager-item span[data-path="${currentItem}"]`);
        if (span) {
            editName(span);
        }
        contextMenu.style.display = 'none';
    }

    function deleteItem() {
        if (confirm("Tem certeza de que deseja apagar este item?")) {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.querySelector(`.file-manager-item span[data-path="${currentItem}"]`).parentElement.remove();
                    } else {
                        alert('Falha ao apagar o item.');
                    }
                }
            };
            xhr.send(`action=delete&currentPath=${encodeURIComponent(currentItem)}`);
        }
        contextMenu.style.display = 'none';
    }

    function EditarVersao() {
        // Exibir um prompt para o usuário inserir a nova versão
        let novaVersao = prompt("Por favor, insira a nova versão (apenas números):");

        // Verificar se o usuário inseriu uma versão e se é um número válido
        if (novaVersao && !isNaN(novaVersao)) {
            novaVersao = Number(novaVersao); // Converter para número
            loadingOverlay.style.display = 'flex'; // Mostrar overlay de carregamento

            let xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // alert('Versão atualizada com sucesso!');
                    } else {
                        alert('Falha ao atualizar a versão.');
                    }
                    loadingOverlay.style.display = 'none';
                }
            };

            // Enviar a requisição para atualizar a versão com o caminho do arquivo e a nova versão
            xhr.send(`action=updateVersion&currentPath=${encodeURIComponent(currentItem)}&novaVersao=${encodeURIComponent(novaVersao)}`);
        } else {
            alert('Por favor, insira um número válido para a versão.');
        }
    }

    function atualizarChecksum() {
        loadingOverlay.style.display = 'flex'; // Mostrar overlay de carregamento

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // alert('Checksum atualizado com sucesso!');
                } else {
                    alert('Falha ao atualizar o checksum.');
                }
                loadingOverlay.style.display = 'none';
            }
        };

        // Enviar a requisição para atualizar o checksum com o caminho do arquivo
        xhr.send(`action=updateChecksum&currentPath=${encodeURIComponent(currentItem)}`);
    }

    function editFile() {
        loadingOverlay.style.display = 'flex';
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    aceEditor.session.setMode(getAceMode(currentItem));
                    aceEditor.setValue(response.fileContent, -1);
                    editorFileName.textContent = currentItem.split('/').pop();
                    // M.Modal.getInstance(editorModal).open();
                    editorModal.style.display = 'block';
                    editorModal.style.width = '100%';
                    editorModal.style.top = '0';
                    loadingOverlay.style.display = 'none';
                } else {
                    alert('Falha ao carregar o conteúdo do arquivo.');
                    loadingOverlay.style.display = 'none';
                }
            }
        };
        xhr.send(`action=getFileContent&currentPath=${encodeURIComponent(currentItem)}`);
        contextMenu.style.display = 'none';
    }

    function FecharEditorDeArquivos() {
        editorModal.style.display = 'none';
    }

    function saveFile() {
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (!response.success) {
                    alert('Falha ao salvar o arquivo.');
                }
            }
        };
        xhr.send(`action=editFile&currentPath=${encodeURIComponent(currentItem)}&fileContent=${encodeURIComponent(aceEditor.getValue())}`);
        // M.Modal.getInstance(editorModal).close();
        editorModal.style.display = 'none';
    }

    function editName(span) {
        let input = span.nextElementSibling;
        input.value = span.textContent;
        span.style.display = 'none';
        input.style.display = 'block';
        input.focus();
    }

    function renameViaInput(input, path) {
        let newName = input.value;
        let span = input.previousElementSibling;

        if (newName !== span.textContent) {
            span.textContent = "renomeando...";
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        span.textContent = response.newName;
                        span.setAttribute('data-path', response.newPath);
                        let icon = getIconJS(response.newName, icons);
                        span.previousElementSibling.src = icon;
                        input.setAttribute('onblur', `renameViaInput(this, '${response.newPath}')`);
                        input.setAttribute('onkeydown', `handleKeyDown(event, this, '${response.newPath}')`);
                    } else {
                        span.textContent = path.split('/').pop(); // revert to original name if failed
                        alert('Falha ao renomear o item.');
                    }
                }
                input.style.display = 'none';
                span.style.display = 'block';
            };
            xhr.send(`action=rename&currentPath=${encodeURIComponent(path)}&newName=${encodeURIComponent(newName)}`);
        } else {
            input.style.display = 'none';
            span.style.display = 'block';
        }
    }

    function createNewFile() {
        let newFileName = prompt("Nome do novo arquivo:");
        if (newFileName) {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        addNewFileItem(response.newFilePath, response.newFileName);
                    } else {
                        alert('Falha ao criar o novo arquivo.');
                    }
                }
            };
            xhr.send(`action=create&currentPath=${encodeURIComponent('<?php echo $current_dir; ?>')}&newFileName=${encodeURIComponent(newFileName)}`);
        }
        contextMenuGeneral.style.display = 'none';
    }

    function createNewFolder() {
        let newFolderName = prompt("Nome da nova pasta:");
        if (newFolderName) {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        addNewFolderItem(response.newFolderPath, response.newFolderName);
                    } else {
                        alert('Falha ao criar a nova pasta.');
                    }
                }
            };
            xhr.send(`action=createFolder&currentPath=${encodeURIComponent('<?php echo $current_dir; ?>')}&newFolderName=${encodeURIComponent(newFolderName)}`);
        }
        contextMenuGeneral.style.display = 'none';
    }

    function addNewFileItem(filePath, fileName) {

        let fileManagerContent = document.getElementById('file-manager-content');
        if (document.querySelector(`.file-manager-item span[data-path="${filePath}"]`)) {
            return; // Se o arquivo já existir, não adiciona novamente
        }
        let newFileItem = document.createElement('div');
        newFileItem.className = 'file-manager-item';
        newFileItem.setAttribute('oncontextmenu', `showContextMenu(event, '${filePath}', false)`);
        newFileItem.setAttribute('onclick', `openFile('${filePath}')`);
        newFileItem.setAttribute('data-size', '0 bytes');
        newFileItem.innerHTML = `
            <img src="${getIconJS(fileName, icons)}" alt="File Icon">
            <span data-path="${filePath}" ondblclick="editName(this)">${truncateName(fileName)}</span>
            <input type="text" class="rename-input" onblur="renameViaInput(this, '${filePath}')" onkeydown="handleKeyDown(event, this, '${filePath}')">
        `;

        insertItemInOrder(fileManagerContent, newFileItem, fileName, false);
    }

    function addNewFolderItem(folderPath, folderName) {
        let fileManagerContent = document.getElementById('file-manager-content');
        if (document.querySelector(`.file-manager-item span[data-path="${folderPath}"]`)) {
            return; // Se a pasta já existir, não adiciona novamente
        }
        let newFolderItem = document.createElement('div');
        newFolderItem.className = 'file-manager-item';
        newFolderItem.setAttribute('onclick', `navigateTo('${folderPath}')`);
        newFolderItem.setAttribute('oncontextmenu', `showContextMenu(event, '${folderPath}', true)`);
        newFolderItem.innerHTML = `
            <img src="${icons['folder']}" alt="Folder Icon">
            <span data-path="${folderPath}" ondblclick="editName(this)">${truncateName(folderName)}</span>
            <input type="text" class="rename-input" onblur="renameViaInput(this, '${folderPath}')" onkeydown="handleKeyDown(event, this, '${folderPath}')">
        `;

        insertItemInOrder(fileManagerContent, newFolderItem, folderName, true);
    }

    function insertItemInOrder(container, newItem, itemName, isDir) {
        let items = Array.from(container.children);
        let inserted = false;
        let i = 1;
        if (isDir) {
            // Inserir pastas na ordem correta
            for (; i < items.length; i++) {
                if (!items[i].querySelector('img').src.includes('folder')) {
                    container.insertBefore(newItem, items[i]);
                    inserted = true;
                    break;
                }
            }
        } else {
            // Inserir arquivos na ordem correta
            for (; i < items.length; i++) {
                if (items[i].querySelector('img').src.includes('folder')) {
                    continue;
                }
                if (items[i].querySelector('span').textContent.localeCompare(itemName) > 0) {
                    container.insertBefore(newItem, items[i]);
                    inserted = true;
                    break;
                }
            }
        }
        if (!inserted) {
            container.appendChild(newItem);
        }
    }

    function handleKeyDown(event, input, path) {
        if (event.key === 'Enter') {
            renameViaInput(input, path);
        }
    }

    function getAceMode(path) {
        const extension = path.split('.').pop();
        switch (extension) {
            case 'js':
                return 'ace/mode/javascript';
            case 'css':
                return 'ace/mode/css';
            case 'html':
            case 'htm':
                return 'ace/mode/html';
            case 'php':
                return 'ace/mode/php';
            case 'json':
                return 'ace/mode/json';
            default:
                return 'ace/mode/text';
        }
    }

    function truncateName(name, maxLength = 20) {
        return name.length > maxLength ? name.substring(0, maxLength) + '...' : name;
    }

    function getIconJS(file, icons) {
        const extension = file.split('.').pop();
        return icons[extension] ?? icons['default']; // Ícone padrão para arquivos desconhecidos
    }

    function triggerUpload() {
        document.getElementById('upload-input').click();
    }

    function uploadFiles(event) {
        const files = event.target.files;
        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('file[]', files[i]);
        }
        formData.append('action', 'uploadFile');
        formData.append('currentPath', '<?php echo $current_dir; ?>');

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                let percentComplete = (event.loaded / event.total) * 100;
                progressBarInner.style.width = percentComplete + '%';
            }
        };
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    response.files.forEach(file => {
                        addNewFileItem(file.filePath, file.fileName);
                    });
                } else {
                    alert('Falha ao fazer o upload dos arquivos.');
                }
                progressBar.style.display = 'none';
                progressBarInner.style.width = '0';
            }
        };
        xhr.send(formData);
        progressBar.style.display = 'block';
    }

    function handleDrop(event) {
        event.preventDefault();
        const files = event.dataTransfer.files;
        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('file[]', files[i]);
        }
        formData.append('action', 'uploadFile');
        formData.append('currentPath', '<?php echo $current_dir; ?>');

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                let percentComplete = (event.loaded / event.total) * 100;
                progressBarInner.style.width = percentComplete + '%';
            }
        };
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    response.files.forEach(file => {
                        addNewFileItem(file.filePath, file.fileName);
                    });
                } else {
                    alert('Falha ao fazer o upload dos arquivos.');
                }
                progressBar.style.display = 'none';
                progressBarInner.style.width = '0';
            }
        };
        xhr.send(formData);
        progressBar.style.display = 'block';
    }

    function allowDrop(event) {
        event.preventDefault();
    }

    function compressFolder() {
        loadingOverlay.style.display = 'flex';
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                loadingOverlay.style.display = 'none';
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    addNewFileItem(response.zipPath, response.zipPath.split('/').pop());
                } else {
                    alert('Falha ao compactar a pasta.');
                }
            }
        };
        xhr.send(`action=compressFolder&currentPath=${encodeURIComponent(currentItem)}`);
        contextMenu.style.display = 'none';
    }

    function downloadFile() {
        window.location.href = `?action=downloadFile&currentPath=${encodeURIComponent(currentItem)}`;
        contextMenu.style.display = 'none';
    }

    function changePermissions() {
        permissionsFileName.textContent = currentItem.split('/').pop();
        M.Modal.getInstance(permissionsModal).open();
        contextMenu.style.display = 'none';
    }

    function copiarLink() {

        console.log(currentItem);

        // Verificar se currentItem foi definido corretamente
        if (typeof currentItem !== 'string' || currentItem === '') {
            console.error('Erro: currentItem não está definido ou não é uma string válida.');
            return;
        }

        // Variável PHP com o caminho da raiz do servidor
        var documentRoot = "<?php echo $documentRoot; ?>";

        // Remover o caminho da raiz do arquivo atual (se existir)
        var caminhoRelativo = currentItem.replace(documentRoot, '');

        // Verificar se o caminho não parece um arquivo (sem extensão) e se não termina com uma barra
        if (!caminhoRelativo.match(/\.[^\/]+$/) && !caminhoRelativo.endsWith('/')) {
            caminhoRelativo += '/'; // Adiciona a barra no final
        }

        // Obter o domínio atual
        var dominioAtual = window.location.origin;

        // Montar a URL completa
        var urlCompleta = dominioAtual + caminhoRelativo;

        // Copiar a URL completa para a área de transferência
        navigator.clipboard.writeText(urlCompleta).then(function() {
            console.log("URL copiada para a área de transferência: " + urlCompleta);
        }).catch(function(err) {
            console.error("Erro ao copiar a URL: ", err);
        });
    }

    function savePermissions() {
        let owner = document.getElementById('permissions-owner').value;
        let group = document.getElementById('permissions-group').value;
        let others = document.getElementById('permissions-others').value;
        let permissions = owner + group + others;

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (!response.success) {
                    alert('Falha ao mudar as permissões.');
                }
            }
        };
        xhr.send(`action=changePermissions&currentPath=${encodeURIComponent(currentItem)}&permissions=${encodeURIComponent(permissions)}`);
        M.Modal.getInstance(permissionsModal).close();
    }

    window.addEventListener('click', function() {
        contextMenu.style.display = 'none';
        contextMenuGeneral.style.display = 'none';
    });

    window.addEventListener('contextmenu', function(event) {
        if (!event.target.closest('.file-manager-item')) {
            showGeneralContextMenu(event);
        } else {
            contextMenuGeneral.style.display = 'none';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        M.Modal.init(document.querySelectorAll('.modal'));
        aceEditor = ace.edit("file-editor");
        aceEditor.setTheme("ace/theme/monokai");
        aceEditor.setOptions({
            autoScrollEditorIntoView: true,
            copyWithEmptySelection: true,
            maxLines: Infinity
        });
    });
</script>
</body>
</html>
