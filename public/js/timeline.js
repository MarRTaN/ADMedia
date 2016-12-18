var mousePos;
var currentTime;
var vid;
var playing;
var isDragging = false;
var isMouseDown = false;

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
		}
	 })
	.mouseup(function(e) {
	    var wasDragging = isDragging;
	    var playBtnWidth = $('.vid-button').width();
		var barWidth = e.clientX - playBtnWidth;
		var timelineWidth = $('.timeline').width();
		var percent = (barWidth*100/timelineWidth);
		$('.current-bar').width(percent+"%");
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
				vid.currentTime = percent * vid.duration / 100;
			}
			if(percent >= 99.9){
				percent = 99.9;
				$('.current-bar').width(percent+"%");
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
			audPlayer.play();
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
	}
}

Object.defineProperty(HTMLMediaElement.prototype, 'playing', {
    get: function(){
        return !!(this.currentTime > 0 && !this.paused && !this.ended && this.readyState > 2);
    }
})

function displayAudioToTimeline(){
	var tlWidth = $('.timeline').width();
	var duration = vid.duration;
	var audioCover = document.getElementById('audio-line-cover');
	var content = "";
	jQuery.each(audioData, function() {
		var start = this['start'];
		var end = this['end'];
		var posStart = start * 100 / duration;
		var posEnd = ( end - start ) * 100 / duration;
		content += "<div id=\"line-"+this['id']+"\" class=\"audio-line\" style=\"margin-left:"+posStart+"%;width:"+posEnd+"%\"></div>"
	});
	audioCover.innerHTML = content;
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
			setTimeout(displayAudioToTimeline, 1000);
        }               
    });
}