<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


<?php
set_time_limit(300); // Increase the maximum execution time to 300 seconds




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['audiofile'])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Create directory if it doesn't exist
    }
    if (!is_writable($targetDir)) {
        die("Upload directory is not writable.");
    }

    $targetFile = $targetDir . basename($_FILES["audiofile"]["name"]);
    $uploadOk = 1;
    $audioFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file is an actual audio file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES["audiofile"]["tmp_name"]);
    finfo_close($finfo);

    $allowedMimeTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg'];
    if (in_array($mimeType, $allowedMimeTypes)) {
        echo "File is an audio file - " . $mimeType . ".<br>";
        $uploadOk = 1;
    } else {
        echo "File is not an audio file.<br>";
        $uploadOk = 0;
    }

    // Check file size (5GB maximum)
    if ($_FILES["audiofile"]["size"] > 5 * 1024 * 1024 * 1024) { // 5GB in bytes
        echo "Sorry, your file is too large.<br>";
        $uploadOk = 0;
    }

    // Check php.ini settings for upload size
    $uploadMaxSize = ini_get('upload_max_filesize');
    $postMaxSize = ini_get('post_max_size');
    $MaxExecSize = ini_get('max_execution_time');
    echo "upload_max_filesize: $uploadMaxSize<br>";
    echo "post_max_size: $postMaxSize<br>";
    echo "max_execution_time: $MaxExecSize<br>";

    // Allow certain file formats
    if ($audioFileType != "mp3" && $audioFileType != "wav" && $audioFileType != "ogg") {
        echo "Sorry, only MP3, WAV, & OGG files are allowed.<br>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.<br>";
    } else {
        if (move_uploaded_file($_FILES["audiofile"]["tmp_name"], $targetFile)) {
            echo "The file " . htmlspecialchars(basename($_FILES["audiofile"]["name"])) . " has been uploaded.<br>";
            // Redirect to the player with the new file
            header("Location: index.php?audiopath=" . urlencode($targetFile));
            exit();
        } else {
            echo "Sorry, there was an error uploading your file.<br>";
        }
    }
}
?>
