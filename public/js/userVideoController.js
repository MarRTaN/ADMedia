$(document).ready(function(){
	$('#jhour').change(function(){
		if($(this).val() > 10){
			$('#jhour').val(10);
		} else if($(this).val() < 0){
			$('#jhour').val(0);
		}
	});

	$('#jmin').change(function(){
		if($(this).val() > 59){
			$('#jmin').val(59);
		} else if($(this).val() < 0){
			$('#jmin').val(0);
		}
	});

	$('#jsec').change(function(){
		if($(this).val() > 59){
			$('#jsec').val(59);
		} else if($(this).val() < 0){
			$('#jsec').val(0);
		}
	});
});

function playVideo(){
	vid.play();
}

function pauseVideo(){
	vid.pause();
}

function jumpTo(){
	var hour = $('#jhour').val() * 3600;
	var min = $('#jmin').val() * 60;
	var sec =  $('#jsec').val() * 1;
	var jumpTime = hour + min + sec;

	if(jumpTime > vid.duration){
		jumpTime = vid.duration;
	}

	vid.currentTime = jumpTime;
	vid.play();
}

function rewind(){
	vid.currentTime = vid.currentTime - 5;
	vid.play();
}

function forward(){
	vid.currentTime = vid.currentTime + 5;
	vid.play();
}

function movieUp(){
	var vol = vid.volume + 0.1;
	if(vol > 1) vol = 1;
	vid.volume = vol;
}

function movieDown(){
	var vol = vid.volume - 0.1;
	if(vol < 0) vol = 0;
	vid.volume = vol;
}

function adUp(){
	var vol = audPlayer.volume + 0.1;
	if(vol > 1) vol = 1;
	audPlayer.volume = vol;
}

function adDown(){
	var vol = audPlayer.volume - 0.1;
	if(vol < 0) vol = 0;
	audPlayer.volume = vol;
}