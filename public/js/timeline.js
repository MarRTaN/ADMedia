var mousePos;
var currentTime;
var vid;
var playing;
var isDragging = false;
var isMouseDown = false;
var audPlayer;
var checkType = 0;

$(document).ready(function(){
	vid = document.getElementById('my-video');
	audPlayer = document.getElementById('audioPlayer');

	var playBtnWidth = $('.play-button').width();
	var videoWidth = $('#my-video').width();
	$('.timeline').width(videoWidth-playBtnWidth);
	$('.drag-area')
	.mousedown(function() {
	    isDragging = false;
	    isMouseDown = true;
	})
	.mousemove(function(e) {
		if(isMouseDown){
			isDragging = true;
		    var playBtnWidth = $('.vid-button').width();
			var barWidth = e.clientX - playBtnWidth;
			var timelineWidth = $('.timeline').width();
			var percent = (barWidth*100/timelineWidth);
			$('.current-bar').width(percent+"%");
			//vid.currentTime = percent * vid.duration / 100;
			setTimer("percent",percent);
		}
	 })
	.mouseup(function(e) {
	    var wasDragging = isDragging;
	    var playBtnWidth = $('.vid-button').width();
		var barWidth = e.clientX - playBtnWidth;
		var timelineWidth = $('.timeline').width();
		var percent = (barWidth*100/timelineWidth);
		$('.current-bar').width(percent+"%");
		audPlayer.pause();
		vid.currentTime = percent * vid.duration / 100;
	    isDragging = false;
	    isMouseDown = false;
	})
	.mouseout(function(e){
		if(isDragging){
			var playBtnWidth = $('.vid-button').width();
			var barWidth = e.clientX - playBtnWidth;
			var timelineWidth = $('.timeline').width();
			var percent = (barWidth*100/timelineWidth);
			if(percent <= 0){
				percent = 0;
				$('.current-bar').width(percent+"%");
				audPlayer.pause();
				vid.currentTime = percent * vid.duration / 100;
			}
			if(percent >= 99.9){
				percent = 99.9;
				$('.current-bar').width(percent+"%");
				audPlayer.pause();
				vid.currentTime = percent * vid.duration / 100;
			}
	    	isDragging = false;
		}
	    isMouseDown = false;
	});

	$('.vid-button').click(function(){
		if(vid.playing){
			vid.pause();
			stopAd();
			audPlayer.pause();
			$('#play-button').hide();
			$('#pause-button').show();
		} else {
			vid.play();
			playAd();
			$('#play-button').show();
			$('#pause-button').hide();
		}
	});
	getAudios();
	playing = setInterval(onPlaying,1000);
});

function onPlaying(){
	if(vid.playing && !isDragging){
		var currentTime = vid.currentTime;
		var duration = vid.duration;
		var percent = (currentTime*100/duration);
		$('.current-bar').width(percent+"%");
		setTimer("time",0);
	}
}

function setTimer(type,val){
	
		var currentTime;

		if(type == "time"){
			currentTime = vid.currentTime;
		} else if(type == "percent"){
			currentTime = vid.duration * val / 100;
		} else {
			return;
		}

		var duration = Math.floor(vid.duration);
		var mhour = Math.floor(duration / 3600);
		var mmin = Math.floor(duration / 60) - (mhour * 60);
		var msec = Math.floor(duration) - (mmin * 60) - (mhour * 3600);
		var msecText = msec;
		if(msec < 10) msecText = "0"+msec;

		var hour = Math.floor(currentTime / 3600);
		var minute = Math.floor(currentTime / 60) - (hour * 60);
		var sec = Math.floor(currentTime) - (minute * 60) - (hour * 3600);
		var secText = sec;
		if(sec < 10) secText = "0"+sec;

	if(document.getElementById('timer') !== null){
		document.getElementById('timer').innerHTML = hour+":"+minute+":"+secText+" | "+mhour+":"+mmin+":"+msecText;
	} else if(document.getElementById('current-time') !== null){
		document.getElementById('current-time').innerHTML = hour+" hours "+minute+" minutes "+sec+" seconds";
	}
}

Object.defineProperty(HTMLMediaElement.prototype, 'playing', {
    get: function(){
        return !!(this.currentTime > 0 && !this.paused && !this.ended && this.readyState > 2);
    }
})

var waitingInterval;
function displayAudioToTimeline(){
	waitingInterval = setInterval(function(){
		// vid.addEventListener("loadeddata", function() {
			setTimer("time",0);
			if(checkType == 0){
			 	var tlWidth = $('.timeline').width();
				var duration = vid.duration;
				var audioCover = document.getElementById('audio-line-cover');
				var content = "";
				jQuery.each(audioData, function() {
					var start = this['start'];
					var end = this['end'];
					var posStart = start * 100 / duration;
					var posEnd = ( end - start ) * 100 / duration;
					content += "<div id=\"line-"+this['id']+"\" class=\"audio-line\" style=\"left:"+posStart+"%;width:"+posEnd+"%\"></div>"
				});
				audioCover.innerHTML = content;
			}
			clearInterval(waitingInterval);
		// });
	},500);
}

function rearrangeAudioData(){
	setAudioList();
	for(a = 0; a < audioList.length - 1; a++){
		i = 0;
		for(j = 1; j < audioList.length; j++){
			var iStart = audioList[i].start;
			var jStart = audioList[j].start;
			var temp;
			if(iStart > jStart) {
				temp = audioList[i];
				audioList[i] = audioList[j];
				audioList[j] = temp;
			}
			i++;
		}
	}
}

function getAudios(){
	var amovie_id = $('#movie_id').val();
	$.ajax({
        url: "/audios",
        type:'GET',
        data:
        {
            movie_id: amovie_id
        },
        success: function(res)
        {	
        	audioData = res;
        	rearrangeAudioData();
			genAudios();
			console.log(audioData);
			setTimeout(displayAudioToTimeline, 1000);
        }               
    });
}



function genAudios(){
	if(checkType == 0){
		var tbody = document.getElementById('audioTbody');
		var content = "";
		var counter = 1;
		var a = audioList;
		for(i = 0; i < a.length; i++){
		  	content += 				'<tr>';
			content +=          	 '<td class="col-md-1 text-center">'+counter+'</td>';
			content +=               '<td class="col-md-6">'+a[i].name+'</td>';
			content +=               '<td class="col-md-1 text-center">'+a[i].start+'</td>';
			content +=               '<td class="col-md-1 text-center">'+a[i].end+'</td>';
			content +=               '<td class="col-md-3 text-center">';
			content +=               	'<form id="del-'+a[i].id+'" action="/delete-audio/'+a[i].movie_id+'/'+a[i].id+'" method="GET"></form>';
			content +=               	'<button class="btn btn-primary" onclick="updateAudio(\''+a[i].movie_id+'\',\''+a[i].id+'\',\''+a[i].name+'\',\''+a[i].file_type+'\',\''+a[i].file+'\',\''+a[i].start+'\',\''+a[i].end+'\');">EDIT</button>';        
			content +=               	'<button class="btn btn-danger" onclick="deleteRecord(\''+a[i].movie_id+'\',\''+a[i].id+'\');">DELETE</button>';
			content +=               '</td>';
			content +=            	'</tr>';
			counter++;
		}
		tbody.innerHTML = content;
	}
}