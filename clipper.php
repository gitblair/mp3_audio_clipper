<?php
    header('Content-Type: application/json');
require "config.php";
    $response = [
        'success' => false,
        'error' => ''
    ];

    $debug = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $startTime = $_POST['startTime'];
        $duration = $_POST['duration'];
        $clipName = $_POST['clipName'];
        $inputFile = $_POST['audiopath'];

        $outputFile = tempnam(sys_get_temp_dir(), 'clip_') . ".mp3";

        $debug[] = "Input file: $inputFile";
        $debug[] = "Start Time: $startTime, Duration: $duration";
        $debug[] = "Clip Name: $clipName, Output File: $outputFile";



        // Ensure paths are properly escaped
        $escapedInputFile = escapeshellarg($inputFile);
        $escapedOutputFile = escapeshellarg($outputFile);
        $escapedStartTime = escapeshellarg($startTime);
        $escapedDuration = escapeshellarg($duration);

        $command = "$ffmpegPath -ss $escapedStartTime -i $escapedInputFile -t $escapedDuration -c copy $escapedOutputFile 2>&1";

        $debug[] = "Executing command: $command";

        exec($command, $output, $return_var);

        if ($return_var === 0) {
            header('Content-Type: audio/mpeg');
            header('Content-Disposition: attachment; filename="' . $clipName . '.mp3"');
            readfile($outputFile);
            unlink($outputFile);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to clip audio', 'debug' => $output]);
        }
    }

    ?>
