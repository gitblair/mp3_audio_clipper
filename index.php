<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Audio Editor</title>

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
  </style>
</head>
<body>
  <div class="container-xxl">
    <div class="row mt-5">
      <h1>Audio Editor</h1>
      <form id="uploadForm" enctype="multipart/form-data" method="post" action="upload.php">
        <div class="mb-3">
          <input class="form-control" type="file" id="formFile" name="audiofile" required>
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
        echo "<ul><li>" . htmlspecialchars($audiopath) . "</li></ul>";

        if (file_exists($audiopath)) {
          $filesize = filesize($audiopath);
          echo "<p>File size: </p>";
          echo "<ul>";
          echo "<li>Bytes: $filesize</li>";
          echo "<li>KB: " . number_format($filesize / 1024, 2) . "</li>";
          echo "<li>MB: " . number_format($filesize / (1024 * 1024), 2) . "</li>";
          echo "<li>GB: " . number_format($filesize / (1024 * 1024 * 1024), 2) . "</li>";
          echo "</ul>";
        }

        echo "<p>Server Stats: </p>";
        echo "<ul>";
        echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
        echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
        echo "<li>max_execution_time: " . ini_get('max_execution_time') . "</li>";
        echo "<li><a href='phpinfo.php'>full php info</a></li>";
        echo "</ul>";
        ?>

        <input type="hidden" id="audiopath" name="audiopath" value="<?php echo $audiopath; ?>">
      </div>

      <div class="col-md-8">
        <div id="waveform"></div>
        <br>
        <button class="btn btn-outline-secondary" onclick="submitClip()">Clip it</button>
      </div>

      <div id="debug"></div>

      <script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.3/wavesurfer.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.3/plugin/wavesurfer.regions.min.js"></script>
      <script>
        let startTime = 0;
        let endTime = 0;

        const wavesurfer = WaveSurfer.create({
          container: "#waveform",
          height: 128,
          waveColor: '#0b273e',
          progressColor: '#3475cd',
          cursorColor: '#ddd5e9',
          cursorWidth: 2,
          fillParent: true,
          url: "<?php echo $audiopath; ?>",
          plugins: [
            WaveSurfer.regions.create()
          ]
        });

        wavesurfer.load('<?php echo $audiopath; ?>');

        wavesurfer.on('ready', () => {
          const duration = wavesurfer.getDuration();
          startTime = 0;
          endTime = duration; // Allow region to cover the entire duration

          wavesurfer.addRegion({
            start: startTime,
            end: endTime,
            color: 'rgba(0, 255, 0, 0.1)',
            drag: true,
            resize: true
          });
        });

        wavesurfer.on('region-updated', (region) => {
          startTime = region.start;
          endTime = region.end;
        });

        function submitClip() {
          const duration = endTime - startTime;

          if (duration <= 0) {
            alert('End time must be greater than start time');
            return;
          }

          const clipName = prompt('Enter the name for the clip file (without extension):');
          if (!clipName) {
            alert('Clip name is required');
            return;
          }

          const formData = new FormData();
          formData.append('startTime', Math.floor(startTime));
          formData.append('duration', Math.floor(duration));
          formData.append('clipName', clipName);
          formData.append('audiopath', "<?php echo $audiopath; ?>");

          fetch('clipper.php', {
            method: 'POST',
            body: formData
          })
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.blob();
          })
          .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `${clipName}.mp3`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            alert('Clip downloaded successfully!');
          })
          .catch(error => {
            console.error('Fetch error:', error);
            alert('Error saving clip: ' + error.message);
          });
        }

        document.addEventListener('keydown', (event) => {
          if (event.code === 'Space') {
            event.preventDefault();
            if (wavesurfer.isPlaying()) {
              wavesurfer.pause();
            } else {
              wavesurfer.play();
            }
          }
        });
      </script>
    </div>
  </div>
</body>
</html>
