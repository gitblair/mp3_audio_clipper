<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Audio Player</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #waveform {
      width: 100%;
      height: 128px;
      background-color: #f2f2f2;
    }
    .controls {
      margin-top: 20px;
      text-align: center;
    }
    .time-indicators {
      margin-top: 10px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container-xxl">


    <div class="row mt-5">
      <form id="uploadForm" enctype="multipart/form-data" method="post" action="upload.php">
        <div class="mb-3">
          <input class="form-control" type="file" id="formFile" name="audiofile">
        </div>
        <div class="col-12">
          <button class="btn btn-primary" type="submit">Upload</button>
        </div>
      </form>
      <div id="error-message" class="text-danger mt-3"></div>
    </div>

    <div class="row mt-5">
            <div class="col-md-4">
              <?php
              if (isset($_GET["audiopath"])) {
                $audiopath = $_GET["audiopath"];
              } else {
                $audiopath = "jazz.mp3"; // Ensure this file is accessible
              }




              echo "<p>File: </p>";
              echo "<ul>";
              echo "<li>";
              echo $audiopath;
              echo "</li>";
              echo "</ul>";





// Display the file size in different units
       if (file_exists($audiopath)) {
         $filesize = filesize($audiopath);
         $size_in_kb = $filesize / 1024;
         $size_in_mb = $size_in_kb / 1024;
         $size_in_gb = $size_in_mb / 1024;

         echo "<p>File size: </p>";
         echo "<ul>";
         echo "<li>Bytes: $filesize</li>";
         echo "<li>KB: " . number_format($size_in_kb, 2) . "</li>";
         echo "<li>MB: " . number_format($size_in_mb, 2) . "</li>";
         echo "<li>GB: " . number_format($size_in_gb, 2) . "</li>";
         echo "</ul>";
       }




       // Check php.ini settings for upload size
       $uploadMaxSize = ini_get('upload_max_filesize');
       $postMaxSize = ini_get('post_max_size');
       $MaxExecSize = ini_get('max_execution_time');
       echo "<p>Server Stats: </p>";
                echo "<ul>";
       echo "<li>upload_max_filesize: $uploadMaxSize</li>";
       echo "<li>post_max_size: $postMaxSize</li>";
       echo "<li>max_execution_time: $MaxExecSize</li>";
       echo "<li><a href='phpinfo.php'>full php info</a></li>";
                echo "</ul>";

              ?>

</div>



      <div class="col-md-8">

        <div id="waveform"></div>

        <script src="https://unpkg.com/wavesurfer.js@7"></script>


<script>







const wavesurfer = WaveSurfer.create({
  "container": "#waveform",
  "height": 128,
  "width": 600,
  "splitChannels": false,
  "normalize": false,
  "waveColor": '#0b273e',
  "progressColor": '#3475cd',
  "cursorColor": '#ddd5e9',
  "cursorWidth": 2,
  "barWidth": null,
  "barGap": null,
  "barRadius": null,
  "barHeight": null,
  "barAlign": "",
  "minPxPerSec": 1,
  "fillParent": true,
  "url": "<?php echo $audiopath; ?>",
  "mediaControls": true,
  "autoplay": false,
  "interact": true,
  "dragToSeek": false,
  "hideScrollbar": false,
  "audioRate": 1,
  "autoScroll": true,
  "autoCenter": true,
  "sampleRate": 8000
})

wavesurfer.on('click', () => {
  wavesurfer.play()
})

</script>


      </div>
    </div>
  </div>
</body>
</html>
