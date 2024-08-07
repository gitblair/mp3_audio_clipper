<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP3 Audio Clipper</title>

    <!-- Bootstrap Style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- App Style -->
    <link rel="stylesheet" href="styles.css">

  </head>
  <body>
    <div class="container-lg mb-5">
      <div class="row">
        <div class="col-12">

          <?php include 'config.php'; ?>
          <h1 class="my-4">MP3 Audio Clipper</h1>

  <form id="uploadForm" class="mb-4" enctype="multipart/form-data" method="post" action="upload.php">
    <div class="mb-3">
      <!-- <label for="fileInput" class="form-label">Upload Audio</label> -->
      <input type="file" id="fileInput" class="form-control" name="audiofile" required <?php echo !$allow_uploads ? 'disabled' : ''; ?>>
    </div>

    <button type="submit" class="btn btn-primary" <?php echo !$allow_uploads ? 'disabled' : ''; ?>>Upload</button>

    <?php if (!$allow_uploads): ?>
        <div class="mt-2 text-danger">Uploads are turned off for this demonstration.</div>
        <div class="mt-2 text-success">A default MP3 is pre-loaded for you to try the clipping functions. Enjoy!</div>
    <?php endif; ?>

    <?php
    if (isset($_GET["audiopath"])) {
    $audiopath = $_GET["audiopath"];
    } else {
    $audiopath = "jazz.mp3"; // Ensure this file is accessible
    }
    ?>

        <input type="hidden" id="audiopath" name="audiopath" value="<?php echo $audiopath; ?>">
    </form>
          <div id="error-message" class="text-danger mt-3"></div>
        </div>


      <div class="col-12">
        <div id="waveform"></div>

        <div class="center-buttons my-2">
              <button id="setInPoint" class="btn btn-primary mx-2">Set IN</button>
              <button id="setOutPoint" class="btn btn-primary mx-2">Set OUT</button>
        </div>

        <br>
        <button id="downloadClip" class="btn btn-success mt-3 mb-5 mx-auto d-block" onclick="submitClip()">Clip & Download</button>
              <!-- <button id="downloadClip" class="btn btn-success mt-3 mb-5 mx-auto d-block">Clip & Download</button> -->
      </div>

      <div id="debug"></div>

      <div class="row">
        <div class="col-12">
          <nav class="text-center">
            <ul class="nav justify-content-center">
              <li class="nav-item">
                <a class="nav-link" href="instructions.html">instructions</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="stats.php?audiopath=<?php echo $audiopath; ?>">stats</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="phpinfo.php">phpinfo</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="attrib.html">attribution</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:history.back()">return</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.3/wavesurfer.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.3/plugin/wavesurfer.regions.min.js"></script>
      <!-- Custom JS -->
      <script>var audiopath = <?php echo json_encode($audiopath); ?>;</script>
      <script src="editing.js"></script> 
    </div>
  </div>
</body>
</html>
