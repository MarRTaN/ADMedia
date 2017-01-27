var interval = 1;
var isAdMuted = false;

var audioList = [];
var audioPlaying= false;

var timer;

function stopAd(){
	if(timer !== null) clearInterval(timer);
}
function playAd(){
	setAudioList();
	timer = setInterval(function() {
		vidPlayer = vid;
		//if(vidPlayer !== undefined && checkIsLoaded()){
			if (!isPlaying(vidPlayer)) {
				audPlayer.pause();
				return;
			}

			if(audioList.length > 0){
				var curVidTime = vidPlayer.currentTime;
				
				var aIdx = -1;

				for (aIdx = 0; aIdx < audioList.length; aIdx++) {
					st = audioList[aIdx].start;
					en = audioList[aIdx].end;
					if (st <= curVidTime && curVidTime <= en){
						break;
					}
				}

				// NO AUDIO DESC NOW
				if (aIdx == -1) {
					audPlayer.pause();
					audPlayer.src = "";
					return;
				}
				var curAud = audioList[aIdx];

				//  
				var source = audPlayer.src

				if(curAud !== undefined){
					if (source === curAud.file) {
						if (!isPlaying(audPlayer) || Math.abs(audPlayer.playbackRate * (curVidTime - curAud.start) - audPlayer.currentTime) > interval) {
							audPlayer.pause();
							audPlayer.playbackRate = audPlayer.duration / (curAud.end - curAud.start);
							audPlayer.currentTime = audPlayer.playbackRate * (curVidTime - curAud.start);
							audPlayer.play();

						}
					}
					else {
						audPlayer.pause();

						var file_type = curAud.file_type;
						if(file_type == 1)	audPlayer.src = upload_path + "/" + curAud.file;
						else				audPlayer.src = curAud.file;


						getduration(audPlayer.src, function(duration){
							//console.log("Playing " + audPlayer.src + ", for: " + duration + "seconds.");
							audPlayer.playbackRate = duration / (curAud.end - curAud.start);
							audPlayer.currentTime = audPlayer.playbackRate * (curVidTime - curAud.start);
						    audPlayer.play(); 
						});

						/*audPlayer.addEventListener('loadedmetadata', function() {
						    console.log("Playing " + audPlayer.src + ", for: " + audPlayer.duration + "seconds.");
							audPlayer.playbackRate = audPlayer.duration / (curAud.end - curAud.start);
							audPlayer.currentTime = audPlayer.playbackRate * (curVidTime - curAud.start);
						    audPlayer.play(); 
						});*/
					}
				}
			}
		//}
		/*else{
			audPlayer.pause();
			vidPlayer.pause();
		}*/

	}, interval * 1000);
}

function checkIsLoaded(){
	if(isPlaying(vidPlayer)){
		return (vidPlayer.buffered.end(0) > vidPlayer.currentTime - 2);
	}
	return false;
}

function isPlaying(media) {
	return !!(media.currentTime > 0 && !media.ended && !media.paused && media.readyState > 2);
}

function muteAudioDescription(){
	document.getElementById('mute').style.display = 'none';
	document.getElementById('unmute').style.display = 'block';
	isAdMuted = true;
	audPlayer.volume = 0;
}

function unmuteAudioDescription(){
	document.getElementById('mute').style.display = 'block';
	document.getElementById('unmute').style.display = 'none';
	isAdMuted = false;
	audPlayer.volume = vidPlayer.volume;
}

function setAudioList(){
	audioList = [];
	index = 0;
	jQuery.each(audioData, function() {
		audioList[index] = {"movie_id": this["movie_id"], "id": this["id"], "name": this["name"], "file": this["file"], "file_type": this["file_type"], "start": this["start"], "end":this["end"]};
		index++;
	});
}


function getduration(url, next) {
    var _player = new Audio(url);
    _player.addEventListener("durationchange", function (e) {
        if (this.duration!=Infinity) {
           var duration = this.duration
           _player.remove();
           next(duration);
        };
    }, false);      
    _player.load();
    _player.currentTime = 24*60*60; //fake big time
    _player.volume = 0;
    _player.play();
    //waiting...
};