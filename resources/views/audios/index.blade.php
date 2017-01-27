@extends('fileupload')

@section('content')
	<!--link rel="stylesheet" type="text/css"  href="{!! asset('css/video-js-5.12.6.css') !!}" />
	<script src="{!! asset('js/video-5.12.6.js') !!}"></script-->
    <script src="{!! asset('js/timeline.js') !!}"></script>
    <script src="{!! asset('js/player.js') !!}"></script>
	<script src="https://cdn.webrtc-experiment.com/RecordRTC.js"></script>
    <script src="https://cdn.webrtc-experiment.com/gif-recorder.js"></script>
    <script src="https://cdn.webrtc-experiment.com/getScreenId.js"></script>

    <!-- for Edige/FF/Chrome/Opera/etc. getUserMedia support -->
	<script src="https://cdn.webrtc-experiment.com/gumadapter.js"></script>

	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<div class="cover">
		<div class="cover-left col-md-7">
			<video id="my-video" class="video-js" preload="auto" data-setup="{}">
				<source src="{{$movie->file}}" type="video/mp4">
				<p class="vjs-no-js">
			      To view this video please enable JavaScript, and consider upgrading to a web browser that
			      <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
			    </p>
			</video>
			<audio id="audioPlayer" preload="metadata"></audio>
			<div class="vid-button">
				<div id="play-button">
					<img src="{!! asset('elements/pause.svg') !!}">
				</div>
				<div id="pause-button">
					<img src="{!! asset('elements/play.svg') !!}">
				</div>
			</div>
			<div class="timeline" id="timeline">
				<div class="current-bar"></div>
				<div class="audio-line-cover" id="audio-line-cover"></div>
				<div class="drag-area"></div>
			</div>
			<div class="timer" id="timer">0:00 | 0:00</div>
			<div class = "well well-audio">
			   <div class="form-cover-audio">
			   		<form id="upload-audio-form">
			   			<div class="col-md-12">
					   	  	<div class="form-group hidden">
					   	  		<input type="text" class="form-control" name="movie_id" id="movie_id" autocomplete="off" value="{{ $movie->id }}" hidden>
						  	</div>
					      	<div class="form-group">
					        	<label for="audio_attachment">Upload method</label><br>
					        	<select class="form-control" name="method" id="audio_method" style="width:10vw">
					        		<option value="none">(Please select)</option>
					        		<option value="link">Link file</option>
					        		<option value="record" id="record-option">Audio record</option>
					        	</select>
					      	</div>
					   	  	<div class="form-group">
					        	<label for="audio_name">Audio Name</label><br>
					   	  		<input type="text" class="form-control" name="name" id="audio_name" autocomplete="off" style="width:12vw">			        
					      	</div>
							<div class="form-group">
								<label> Start time : </label><br>
								<input type="number" min="0" class="time-input time-input-h form-control" id="start-h" max="4" value="0">:
								<input type="number" min="0" class="time-input time-input-m form-control" id="start-m" max="59" value="0">:
								<input type="number" min="0" class="time-input time-input-s form-control" id="start-s" max="59" value="0">
								<input type="text" name="start" id="audio_start" value="0" hidden>
							</div>
							<div class="form-group">
								<label> End time : </label><br>
								<input type="number" min="0" class="time-input time-input-h form-control" id="end-h" max="4" value="0">:
								<input type="number" min="0" class="time-input time-input-m form-control" id="end-m" max="59" value="0">:
								<input type="number" min="0" class="time-input time-input-s form-control" id="end-s" max="59" value="0">
								<input type="text" name="end" id="audio_end" value="0" hidden>
							</div>
						</div>
						<div class="col-md-12" id="upload-part">
					      	<div class="form-group" id="audio_file_cover">
					        	<label for="audio_attachment">Link File</label><br>
					        	<input type="text" class="form-control input-file-text" name="path" id="audio_attachment" style="width: 40vw;">
					      	</div>
					      	<div class="recordrtcc" id="audio_record">
					      		<label for="audio_attachment">Audio Recorder</label><br>
					            <div class="btn btn-success" id="record-btn" onclick="clickToRecord(this);">Start Recording</div>
					            <video controls muted style="display:none;"></video>
					            <div class="audio-cover">
					            	<audio src="" id="sample"></audio>
					            </div>
					        </div>
						    <div class="form-group">
						    	<label class="label-hidden" for="audio_upload">Upload</label><br>
							    <div onclick="uploadAudio();" id="upload-btn" class="btn btn-primary" disabled="disabled" style="position: absolute; right:1vw">Upload</div>
							</div>
						</div>
					</form>
			   </div>
			</div>

		</div>
		<div class="cover-right col-md-5">
				<h2>Audio Upload for <b>{{ $movie->name }}</b></h2>
				<!--<?php if(count($audios) > 0) { ?>
				  <table class = "table table-bordered table-striped fixed-header audio-table" id="audio-list">
				     <thead>
				        <tr>
				           <th class="col-md-1 text-center">No</th>
				           <th class="col-md-6 text-center">Audio Name</th>
				           <th class="col-md-1 text-center">Start</th>
				           <th class="col-md-1 text-center">End</th>
				           <th class="col-md-3 text-center"></th>
				        </tr>
				     </thead>
				     <?php $x = 0 ?>
				     <tbody id="audioTbody">
				     	@foreach($audios as $audio)
				           <tr>
				          	  <td class="col-md-1 text-center">{{ $x = $x + 1 }}</td>
				              <td class="col-md-6">{{ $audio->name }}</td>
				              <td class="col-md-1 text-center">{{ $audio->start }}</td>
				              <td class="col-md-1 text-center">{{ $audio->end }}</td>
				              <td class="col-md-3 text-center">
				              	<form id="del-{{ $audio->id }}" action="/delete-audio/{{$movie->id}}/{{ $audio->id }}" method="GET"></form>
				              	<button class="btn btn-primary" onclick="updateAudio('{{$movie->id}}','{{ $audio->id }}','{{$audio->name}}','{{$audio->file}}','{{$audio->start}}','{{$audio->end}}');">EDIT</button>         
				              	<button class="btn btn-danger" onclick="deleteRecord('{{$movie->id}}','{{ $audio->id }}');">DELETE</button>
				              </td>
				           </tr>
				        @endforeach
				     </tbody>
				  </table>
				<?php } ?>-->
				<table class = "table table-bordered table-striped fixed-header audio-table" id="audio-list">
				     <thead>
				        <tr>
				           <th class="col-md-1 text-center">No</th>
				           <th class="col-md-6 text-center">Audio Name</th>
				           <th class="col-md-1 text-center">Start</th>
				           <th class="col-md-1 text-center">End</th>
				           <th class="col-md-3 text-center"></th>
				        </tr>
				     </thead>
				     <tbody id="audioTbody">
				     </tbody>
				  </table>
		</div>
	</div>

	<div class="popup">
		<div class="box">
			<div class="confirm-box">
				<h3>Comfirm to delete this audio</h3>
				<button class="btn btn-success" onclick="confirmDeleteAudio();">YES</button>
				<button class="btn btn-danger" onclick="popOut();">NO</button>
			</div>
		</div>
	</div>
	<div class="update-popup">
		<div class="update-box">
			<form id="update-box" method="GET" class="confirm-box" action="/update-audio">
				<h3>Update Audio Description Infomation</h3>
				<label class="col-md-2">Name</label>
				<input type="hidden" class="form-control" name="movie_id" id="update_movie_id" autocomplete="off">
				<input type="hidden" class="form-control" name="id" id="update_id" autocomplete="off">
				<div class="col-md-10">
					<input type="text" class="form-control" name="name" id="update_name" autocomplete="off">
				</div>
				<label class="col-md-2">Link File</label>
				<div class="col-md-10">
					<input type="text" class="form-control" name="path" id="update_path" autocomplete="off">
			    </div>
				<div class="form-group col-md-6">
						<label style="margin-right:10px;">Start</label>
						<input type="hidden" name="start" id="update_start" autocomplete="off">
						<input type="number" min="0" class="time-input time-input-h form-control" id="update-start-h" max="4" value="0">:
						<input type="number" min="0" class="time-input time-input-m form-control" id="update-start-m" max="59" value="0">:
						<input type="number" min="0" class="time-input time-input-s form-control" id="update-start-s" max="59" value="0">
						<input type="text" name="audio_start" id="audio_start" value="0" hidden>
			    </div>
				<div class="form-group col-md-6">
						<label style="margin-right:10px;">End</label>
						<input type="hidden" name="end" id="update_end" autocomplete="off">
						<input type="number" min="0" class="time-input time-input-h form-control" id="update-end-h" max="4" value="0">:
						<input type="number" min="0" class="time-input time-input-m form-control" id="update-end-m" max="59" value="0">:
						<input type="number" min="0" class="time-input time-input-s form-control" id="update-end-s" max="59" value="0">
						<input type="text" name="audio_end" id="audio_end" value="0" hidden>
			    </div>
			</form>
			<div class="btn btn-success" id="audio-update-confirm" onclick="confirmUpdateAudio();">SAVE</div>
			<div class="btn btn-danger" onclick="popOut();">CANCEL</div>
		</div>
	</div>
	<script type="text/javascript">
		var audioData;
		var audioList = [];
		var type = 'audio';
		var vid = document.getElementById('my-video');
		var leftWidth = $('.cover-left').width();
		$('#my-video').attr('width',leftWidth);

	</script>
	<!-- for audio record -->
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
                                if(!button.recordRTC[1]) {
                                    button.recordingEndedCallback(url);
                                    stopStream();

                                    //saveToDiskOrOpenNewTab(button.recordRTC[0]);
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
                                button.recordingEndedCallback(url);
                                stopStream();

                                //saveToDiskOrOpenNewTab(button.recordRTC);
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
                        //var audio = new Audio();
                        var audio = document.getElementById('sample');
                        audio.src = url;
                        audio.controls = true;
                        //recordingPlayer.parentNode.appendChild(document.createElement('hr'));
                        //recordingPlayer.parentNode.appendChild(audio);

                        if(audio.paused) audio.play();
                        audioBlob = button.recordRTC.blob;

                        audio.onended = function() {
                            audio.pause();
                            audio.src = URL.createObjectURL(button.recordRTC.blob);
                            checkUploadFill();
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
            	var elem = document.getElementById("audio_record");
    			elem.innerHTML = "Audio record is not supported for Chrome. Please use Firefox browser to activate this function."
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
          var upload_path = "{!! asset('upload') !!}";
          function changeRecordBtnStyle(type){
            var button = document.getElementById('record-btn');
            if(type == 'start'){
              button.innerHTML = "Stop";
            } else if(type == 'stop'){
              button.innerHTML = "Start";
            }
          }
          /*function uploadRecord(){
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
                    alert(data);
                },    
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    document.getElementById('error').innerHTML = xhr.responseText;
                }
            });
          }*/
          function getFormData(){
			var data = new FormData();
            data.append('file', audioBlob);
            data.append('_token', '{{ csrf_token() }}');
            return data;
          }
        </script>
@stop