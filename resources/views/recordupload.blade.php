<!DOCTYPE html>
<html lang="en">

<head>

    <script src="https://cdn.webrtc-experiment.com/RecordRTC.js"></script>
    <script src="https://cdn.webrtc-experiment.com/gif-recorder.js"></script>
    <script src="https://cdn.webrtc-experiment.com/getScreenId.js"></script>
    <script src="{!! asset('js/jquery-3.1.0.min.js') !!}"></script>

    <!-- for Edige/FF/Chrome/Opera/etc. getUserMedia support -->
    <script src="https://cdn.webrtc-experiment.com/gumadapter.js"></script>
</head>

<body>
    <article>
        <form action="/upload-record" method="post" enctype="multipart/form-data">
            Select image to upload:
            {{ csrf_field() }}
            <input type="hidden" name="audioUrl" id="fileToUpload">
            <input type="submit" value="Upload Image" name="submit">
        </form>

        <section class="recordrtcc">
            <h2 class="header">
                <button class="record-btn" id="record-btn" onclick="clickToRecord(this);">Start Recording</button>
            </h2>

            <div style="text-align: center; display: none;">
                <button id="save-to-disk">Save To Disk</button>
                <button id="open-new-tab">Open New Tab</button>
            </div>

            <br>

            <video controls muted></video>
        </section>

        <div id="error" style="width:100%;height:100%;">

        </div>

        <script>
            (function() {
                var params = {},
                    r = /([^&=]+)=?([^&]*)/g;

                function d(s) {
                    return decodeURIComponent(s.replace(/\+/g, ' '));
                }

                var match, search = window.location.search;
                while (match = r.exec(search.substring(1))) {
                    params[d(match[1])] = d(match[2]);

                    if(d(match[2]) === 'true' || d(match[2]) === 'false') {
                        params[d(match[1])] = d(match[2]) === 'true' ? true : false;
                    }
                }

                window.params = params;
            })();

            function addStreamStopListener(stream, callback) {
                var streamEndedEvent = 'ended';

                if ('oninactive' in stream) {
                    streamEndedEvent = 'inactive';
                }

                stream.addEventListener(streamEndedEvent, function() {
                    callback();
                    callback = function() {};
                }, false);

                stream.getAudioTracks().forEach(function(track) {
                    track.addEventListener(streamEndedEvent, function() {
                        callback();
                        callback = function() {};
                    }, false);
                });

                stream.getVideoTracks().forEach(function(track) {
                    track.addEventListener(streamEndedEvent, function() {
                        callback();
                        callback = function() {};
                    }, false);
                });
            }
        </script>

        <script>

            var recordingDIV = document.querySelector('.recordrtcc');
            var recordingPlayer = recordingDIV.querySelector('video');
            var recordButton = document.getElementById('record-btn');
            var isRecording = false;
            var currentRecordUrl = "";
            var audioBlob;

            window.onbeforeunload = function() {
                recordingDIV.querySelector('button').disabled = false;
            };

            function clickToRecord(btn) {
                var button = btn;

                if(isRecording) {
                    button.disabled = true;
                    button.disableStateWaiting = true;
                    setTimeout(function() {
                        button.disabled = false;
                        button.disableStateWaiting = false;
                    }, 2 * 1000);

                    function stopStream() {
                        if(button.stream && button.stream.stop) {
                            button.stream.stop();
                            button.stream = null;
                        }
                    }

                    if(button.recordRTC) {
                        if(button.recordRTC.length) {
                            button.recordRTC[0].stopRecording(function(url) {
                                document.getElementById('fileToUpload').innerHTML = url;
                                if(!button.recordRTC[1]) {
                                    button.recordingEndedCallback(url);
                                    stopStream();

                                    saveToDiskOrOpenNewTab(button.recordRTC[0]);
                                    return;
                                }

                                button.recordRTC[1].stopRecording(function(url) {
                                    button.recordingEndedCallback(url);
                                    stopStream();
                                });
                            });
                        }
                        else {
                            button.recordRTC.stopRecording(function(url) {
                                document.getElementById('fileToUpload').innerHTML = url;
                                button.recordingEndedCallback(url);
                                stopStream();

                                saveToDiskOrOpenNewTab(button.recordRTC);
                            });
                        }
                    }

                    return;
                }

                button.disabled = true;

                var commonConfig = {
                    onMediaCaptured: function(stream) {
                        button.stream = stream;
                        if(button.mediaCapturedCallback) {
                            button.mediaCapturedCallback();
                        }
                        changeRecordBtnStyle("start");
                        isRecording = true;
                        button.disabled = false;
                    },
                    onMediaStopped: function() {
                        changeRecordBtnStyle("stop");
                        isRecording = false;

                        if(!button.disableStateWaiting) {
                            button.disabled = false;
                        }
                    },
                    onMediaCapturingFailed: function(error) {
                        if(error.name === 'PermissionDeniedError' && !!navigator.mozGetUserMedia) {
                            intallFirefoxScreenCapturingExtension();
                        }

                        commonConfig.onMediaStopped();
                    }
                };

                var mimeType = 'video/webm';

                captureAudio(commonConfig);

                button.mediaCapturedCallback = function() {
                    var options = {
                        type: 'audio',
                        mimeType: mimeType,
                        bufferSize: typeof params.bufferSize == 'undefined' ? 0 : parseInt(params.bufferSize),
                        sampleRate: typeof params.sampleRate == 'undefined' ? 44100 : parseInt(params.sampleRate),
                        leftChannel: params.leftChannel || false,
                        disableLogs: params.disableLogs || false,
                        recorderType: webrtcDetectedBrowser === 'edge' ? StereoAudioRecorder : null
                    };

                    console.log(mimeType);

                    if(typeof params.sampleRate == 'undefined') {
                        delete options.sampleRate;
                    }

                    button.recordRTC = RecordRTC(button.stream, options);

                    button.recordingEndedCallback = function(url) {
                        var audio = new Audio();
                        audio.src = url;
                        audio.controls = true;
                        recordingPlayer.parentNode.appendChild(document.createElement('hr'));
                        recordingPlayer.parentNode.appendChild(audio);

                        if(audio.paused) audio.play();
                        audioBlob = button.recordRTC.blob;

                        audio.onended = function() {
                            audio.pause();
                            audio.src = URL.createObjectURL(button.recordRTC.blob);
                        };
                    };

                    button.recordRTC.startRecording();
                };
            };

            function captureAudio(config) {
                captureUserMedia({audio: true}, function(audioStream) {
                    recordingPlayer.srcObject = audioStream;
                    recordingPlayer.play();

                    config.onMediaCaptured(audioStream);

                    addStreamStopListener(audioStream, function() {
                        config.onMediaStopped();
                    });
                }, function(error) {
                    config.onMediaCapturingFailed(error);
                });
            }

            function captureUserMedia(mediaConstraints, successCallback, errorCallback) {
                var isBlackBerry = !!(/BB10|BlackBerry/i.test(navigator.userAgent || ''));
                if(isBlackBerry && !!(navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia)) {
                    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
                    navigator.getUserMedia(mediaConstraints, successCallback, errorCallback);
                    return;
                }

                navigator.mediaDevices.getUserMedia(mediaConstraints).then(successCallback).catch(errorCallback);
            }

            if(webrtcDetectedBrowser === 'edge') {
                // webp isn't supported in Microsoft Edge
                // neither MediaRecorder API
                // so lets disable both video/screen recording options
            }

            if(webrtcDetectedBrowser === 'firefox') {
                // Firefox implemented both MediaRecorder API as well as WebAudio API
                // Their MediaRecorder implementation supports both audio/video recording in single container format
                // Remember, we can't currently pass bit-rates or frame-rates values over MediaRecorder API (their implementation lakes these features)
            }

            if(webrtcDetectedBrowser === 'chrome') {

            }

            function saveToDiskOrOpenNewTab(recordRTC) {
                recordingDIV.querySelector('#save-to-disk').parentNode.style.display = 'block';
                recordingDIV.querySelector('#save-to-disk').onclick = function() {
                    if(!recordRTC) return alert('No recording found.');

                    recordRTC.save();
                };

                recordingDIV.querySelector('#open-new-tab').onclick = function() {
                    if(!recordRTC) return alert('No recording found.');

                    window.open(recordRTC.toURL());
                };
            }
        </script>
        <script type="text/javascript">
          function changeRecordBtnStyle(type){
            var button = document.getElementById('record-btn');
            if(type == 'start'){
              button.innerHTML = "Stop";
            } else if(type == 'stop'){
              button.innerHTML = "Start";
            }
          }
          function uploadAudio(){
            var data = new FormData();
            data.append('file', audioBlob);
            data.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url :  "/upload-record",
                type: 'POST',
                data: data,
                contentType: false,
                processData: false,
                success: function(data) {
                    console.log("Success");
                    console.log(data);
                },    
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    document.getElementById('error').innerHTML = xhr.responseText;
                }
            });
          }
        </script>
    </article>
</body>

</html>
