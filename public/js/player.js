var interval = 0.2;
var isAdMuted = false;

var audioList = [];
var audioPlaying= false;

var timer;
var audPlayer;

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
						audPlayer.src = curAud.file;
						if(!isNaN(audPlayer.duration)){
							audPlayer.playbackRate = audPlayer.duration / (curAud.end - curAud.start);
							audPlayer.currentTime = audPlayer.playbackRate * (curVidTime - curAud.start);
							audPlayer.play();
						}
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
		audioList[index] = {"movie_id": this["movie_id"], "id": this["id"], "name": this["name"], "file": this["file"], "start": this["start"], "end":this["end"]};
		index++;
	});
}