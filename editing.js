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
  url: audiopath,
  plugins: [
    WaveSurfer.regions.create()
  ]
});

wavesurfer.load(audiopath);

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

  const clipName = prompt(`Start: ${startTime}\nEnd: ${endTime}\nEnter the name for the clip file (without extension):`);

  if (!clipName) {
    alert('Clip name is required');
    return;
  }

  const formData = new FormData();
  formData.append('startTime', Math.floor(startTime));
  formData.append('duration', Math.floor(duration));
  formData.append('clipName', clipName);
  formData.append('audiopath', audiopath);

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
  } else if (event.code === 'KeyI') {
    startTime = wavesurfer.getCurrentTime();
    updateRegion();
  } else if (event.code === 'KeyO') {
    endTime = wavesurfer.getCurrentTime();
    updateRegion();
  }
});

document.getElementById('setInPoint').addEventListener('click', () => {
  startTime = wavesurfer.getCurrentTime();
  updateRegion();
});

document.getElementById('setOutPoint').addEventListener('click', () => {
  endTime = wavesurfer.getCurrentTime();
  updateRegion();
});

function updateRegion() {
  wavesurfer.clearRegions();
  wavesurfer.addRegion({
    start: startTime,
    end: endTime,
    color: 'rgba(0, 255, 0, 0.1)',
    drag: true,
    resize: true
  });
}
