<!DOCTYPE html>
<html>
   <head>
      <title>Movie | {{ $movie->name }}</title>
      <link rel="stylesheet" type="text/css"  href="{!! asset('css/player.css') !!}" />
      <link rel="stylesheet" type="text/css"  href="{!! asset('css/fileupload.css') !!}" />
      <link rel="stylesheet" type="text/css"  href="{!! asset('css/app.css') !!}" />
      <script src="{!! asset('js/jquery-3.1.0.min.js') !!}"></script>
      <script src="{!! asset('js/timeline.js') !!}"></script>
      <script src="{!! asset('js/player.js') !!}"></script>
      <script src="{!! asset('js/userVideoController.js') !!}"></script>
   </head>
   
   <body>
   	<div class="body-cover"></div>
   	  <div class="container">
   		<h1 class="title">คุณกำลังชม {{ $movie->name }}</h1>

	  	<div class="button-cover">
		  	<button class="btn btn-primary" onclick="playVideo();">Play video</button>
		  	<button class="btn btn-primary" onclick="pauseVideo();">Pause video</button>
		  	<button class="btn btn-success" onclick="rewind();">Rewind 5 Seconds</button>
		  	<button class="btn btn-success" onclick="forward();">Forward 5 Seconds</button>
		  	<button class="btn btn-danger" onclick="movieUp();">Volume Movie Up</button>
		  	<button class="btn btn-danger" onclick="movieDown();">Volume Movie Down</button>
		  	<button class="btn btn-warning" onclick="adUp();">Volume AD Up</button>
		  	<button class="btn btn-warning" onclick="adDown();">Volume AD Down</button>
	  	</div>

	  	<div class="button-cover">
	  		<label>Current time</label>
	  		<label id="current-time">0 hours 0 minutes 0 seconds</label>
	  		<label>Jump to</label>
	  		<label> Hours </label>
	  		<input type="number" value="0" id="jhour" min="0" max="10">
	  		<label> Minutes </label>
	  		<input type="number" value="0" id="jmin" min="0" max="59">
	  		<label> Seconds </label>
	  		<input type="number" value="0" id="jsec" min="0" max="59">
		  	<button class="btn btn-default" style="margin-left: 2vw;" onclick="jumpTo();">Jump</button>
	  	</div>

   		<div class="video-cover">
			<video id="my-video" class="video-js player-video" preload="auto" data-setup="{}" controls>
				<source src="{{$movie->file}}" type="video/mp4">
				<p class="vjs-no-js">
			      To view this video please enable JavaScript, and consider upgrading to a web browser that
			      <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
			    </p>

			</video>
			<audio id="audioPlayer" preload="metadata"></audio>
			<div class="form-group hidden">
	   	  		<input type="text" class="form-control" name="movie_id" id="movie_id" autocomplete="off" value="{{$movie->id}}" hidden="">
		  	</div>
	  	</div>
	  </div>
   </body>

	<script type="text/javascript">
		var upload_path = "{!! asset('upload') !!}";
		var audioData;
		var audioList = [];
		var type = 'audio';
		var vid = document.getElementById('my-video');
		//$('#my-video').attr('width','100%');
		$(document).ready(function(){
			rearrangeAudioData();
			checkType = 1111;
			vid.addEventListener('playing', function(){
		        vid.play();
				playAd();
		    });
		    vid.addEventListener('pause', function(){
	            vid.pause();
				stopAd();
				audPlayer.pause();
		    });
		});

	</script>

</html>