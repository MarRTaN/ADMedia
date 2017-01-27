var deleteId = -1;
var deleteMovieId = "";
var updateId = -1;
var updateName = "";
var updateFile = "";
var updateMovieId = "";
var editMode = false;

$(document).ready(function(){
	setTimeFieldAction();
	if(type !== 'movie') {
		rearrangeAudioData();
		genAudios();

	    $('#update_name').keyup(function(){
	    	checkUpdateFill()
	    });
	    $('#update_path').keyup(function(){
	    	checkUpdateFill()
	    });
	    $('#update_start').keyup(function(){
	    	checkUpdateFill()
	    });
	    $('#update_end').keyup(function(){
	    	checkUpdateFill()
	    });
	    $('#audio_name').keyup(function(){
	    	checkUploadFill()
	    });
	    $('#audio_attachment').keyup(function(){
	    	checkUploadFill()
	    });
	    $('#audio_method').change(function(){
	    	console.log($(this).val());
	    	var val = $(this).val();
	    	if(val !== "none"){
	    		$('#upload-part').show();
	    		if(val == "link"){
	    			$('#audio_file_cover').show();
	    			$('#audio_record').hide();
	    			$('#upload-btn').show();
	    		} else {
	    			$('#audio_file_cover').hide();
	    			$('#audio_record').show();
	    			if(webrtcDetectedBrowser === 'chrome') {
	    				$('#upload-btn').hide();
	    			}
	    		}
	    	} else {
	    		$('#upload-part').hide();
	    	}
	    });
	    document.getElementById('audio_method').value = "none";
	
	}
});

function deleteRecord(movie_id,id){
	$('.popup').fadeIn(10);
	deleteId = id;
	deleteMovieId = movie_id;
}

function deleteRecord(id){
	$('.popup').fadeIn(10);
	deleteId = id;
}

function confirmDelete(){
	if(deleteId != -1){
		var form = document.getElementById('del-'+deleteId);
		form.submit();
	}
	popOut();
}

function confirmDeleteAudio(){
	if(deleteId != -1){
		$.ajax({
	        url: "/delete-audio",
	        type:'GET',
	        data:
	        {
	            movie_id: deleteMovieId,
	            id: deleteId
	        },
	        success: function(res)
	        {
	           audioData = res;
	           rearrangeAudioData();
	           genAudios();
	           setTimeout(displayAudioToTimeline, 100);
	        }               
	    });

	}
	popOut();
}

function updateMovie(id,name,file){
	$('#update_id').val(id);
	$('#update_name').val(name);
	$('#update_path').val(file);
	$('.update-popup').fadeIn(10);
	updateName = name;
	updateFile = file;
	updateId = id;
	editMode = true;
}

function updateAudio(movie_id,id,name,file_type,file,start,end){
	$('#update_movie_id').val(movie_id);
	$('#update_id').val(id);
	$('#update_name').val(name);
	$('#update_path').val(file);
	$('#update_start').val(start);
	$('#update_end').val(end);
	$('.update-popup').fadeIn(10);
	updateName = name;
	updateFile = file;
	updateStart = start;
	updateEnd = end;
	updateId = id;
	updateMovieId = movie_id;
	editMode = true;
	setTimeFieldValue();
}


function confirmUpdate(){
	var form = document.getElementById('update-box');
	form.submit();
}

function confirmUpdateAudio(){
	var amovie_id = $('#update_movie_id').val();
	var aid = $('#update_id').val();
	var aname = $('#update_name').val();
	var afile = $('#update_path').val();
	var astart = $('#update_start').val();
	var aend = $('#update_end').val();

	console.log(aid);
	console.log()
	var isOverlap = checkOverlap(astart,aend,aid);

	if(!isOverlap){
	    popOut();
		$.ajax({
	        url: "/update-audio",
	        type:'GET',
	        data:
	        {
	            movie_id: amovie_id,
	            id: aid,
	            name: aname,
	            path: afile,
	            start: astart,
	            end: aend
	        },
	        success: function(res)
	        {
	            audioData = res;
	            rearrangeAudioData();
	            genAudios();
	            setTimeout(displayAudioToTimeline, 100);
	        }               
	    });
	} else {
		alert("The time is overlap the other audios");
	}
}

function popOut(){
	$('.popup').fadeOut(10);
	$('.update-popup').fadeOut(10);
	deleteId = -1;
	updateIdId = -1;
	updateName = "";
	updateFile = "";
	editMode = false;
}


function uploadMovie(){
	var form = document.getElementById('upload-movie-form');
	form.submit();
}

function uploadAudio(){
	if($('#upload-btn').attr('disabled') !== 'disabled'){
		var amovie_id = $('#movie_id').val();
		var aname = $('#audio_name').val();
		var astart = $('#audio_start').val();
		var aend = $('#audio_end').val();
		var afile_type, afile;

		if(astart == aend){
			alert("Start and end time should not be equal");
			return;
		}

		var isOverlap = checkOverlap(astart,aend,-100);
		if(!isOverlap){

			var audio_method = $('#audio_method').val();
			if(audio_method == "link") {

				afile_type = 0;
				afile = $('#audio_attachment').val();

				$.ajax({
			        url: "/upload-audio",
			        type:'GET',
			        data:
			        {
			            movie_id: amovie_id,
			            name: aname,
			            path: afile,
			            file_type: afile_type,
			            start: astart,
			            end: aend
			        },
			        success: function(res)
			        {	
			           audioData = res;
			           rearrangeAudioData();
			           genAudios();
			           setTimeout(displayAudioToTimeline, 100);
			        }               
			    });
			}
			else if(audio_method == "record") {

				var audio_data = getFormData();

	            $.ajax({
	                url :  "/upload-record",
	                type: 'POST',
	                data: audio_data,
	                contentType: false,
	                processData: false,
	                success: function(path) {
	                    console.log("Success");
						afile_type = 1;
						afile = path;
						$.ajax({
					        url: "/upload-audio",
					        type:'GET',
					        data:
					        {
					            movie_id: amovie_id,
					            name: aname,
					            path: afile,
					            file_type: afile_type,
					            start: astart,
					            end: aend
					        },
					        success: function(res)
					        {	
					           audioData = res;
					           rearrangeAudioData();
					           genAudios();
					           setTimeout(displayAudioToTimeline, 100);
					        }               
					    });
	                },    
	                error: function(xhr, status, error) {
	                	alert("Upload fail");
	                    //console.log(xhr.responseText);
	                    //document.getElementById('error').innerHTML = xhr.responseText;
	                }
	            });
			}
			else { alert("File type does not match"); return; }
		} else {
			alert("The time is overlap the other audios");
		}
		
	}
}

function setTimeUpload(){
    var h = parseInt(document.getElementById('start-h').value);
    var m = parseInt(document.getElementById('start-m').value);
    var s = parseInt(document.getElementById('start-s').value);
    var startTime = (h*3600) + (m*60) + s;

    var eh = parseInt(document.getElementById('end-h').value);
    var em = parseInt(document.getElementById('end-m').value);
    var es = parseInt(document.getElementById('end-s').value);
    var endTime = (eh*3600) + (em*60) + es;

    if(startTime > endTime){
      endTime = startTime;
      document.getElementById('end-h').value = document.getElementById('start-h').value;
      document.getElementById('end-m').value = document.getElementById('start-m').value;
      document.getElementById('end-s').value = document.getElementById('start-s').value;
    }

    if(!editMode){
	    $('#audio_start').val(startTime);
	    $('#audio_end').val(endTime);
	}
}

function setTimeUpdate(){
    var h = parseInt(document.getElementById('update-start-h').value);
    var m = parseInt(document.getElementById('update-start-m').value);
    var s = parseInt(document.getElementById('update-start-s').value);
    var startTime = (h*3600) + (m*60) + s;

    var eh = parseInt(document.getElementById('update-end-h').value);
    var em = parseInt(document.getElementById('update-end-m').value);
    var es = parseInt(document.getElementById('update-end-s').value);
    var endTime = (eh*3600) + (em*60) + es;

    if(startTime > endTime){
      endTime = startTime+1;
      document.getElementById('update-end-h').value = document.getElementById('update-start-h').value;
      document.getElementById('update-end-m').value = document.getElementById('update-start-m').value;
      document.getElementById('update-end-s').value = document.getElementById('update-start-s').value;
    }

    if(editMode){
		$('#update_start').val(startTime);
		$('#update_end').val(endTime);
	}
}

function checkUpdateFill(){
	var name = $('#update_name').val();
	var path = $('#update_path').val();
	var start = $('#update_start').val();
	var end = $('#update_end').val();
	if(start !== undefined && end !== undefined){
		if(name == "" || path == "" || start == "" || end == ""){
			$('#audio-update-confirm').attr('disabled','disabled');
		} else {
			if(isNumeric(start) && isNumeric(end)){
				$('#audio-update-confirm').removeAttr('disabled');
			} else {
				$('#audio-update-confirm').attr('disabled','disabled');
			}
		}
	} else {
		if(name == "" || path == ""){
			$('#movie-update-confirm').attr('disabled','disabled');
		} else {
			$('#movie-update-confirm').removeAttr('disabled');
		}
	}

	if(updateId != -1){
		updateName = $('#update_name').val();
		updateFile = $('#update_path').val();
		if(type == 'audio'){
			updateStart = $('#update_start').val();
			updateEnd = $('#update_end').val();
		}
	}
}

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function checkUploadFill(){
	var name = $('#audio_name').val();
	var path = $('#audio_attachment').val();
	var method = $('#audio_method').val();
	if(method == "link"){
		if(name == "" || path == ""){
			$('#upload-btn').attr('disabled','disabled');
		} else {
			$('#upload-btn').removeAttr('disabled');
		}
	} else if(method == "record"){
		if($('#sample').attr('src') == "" || name == "") {
			$('#upload-btn').attr('disabled','disabled');
		} else {
			$('#upload-btn').removeAttr('disabled');
		}
	}
}

function setTimeFieldAction(){
	$('.time-input-m').click(function(){
      if($(this).val() > 59) $(this).val(59);
      else if($(this).val() < 0) $(this).val(0);
      else if($(this).val() == "") $(this).val(0);
      if(!editMode) setTimeUpload();
      else			setTimeUpdate();
    });
    $('.time-input-s').click(function(){
      if($(this).val() > 59) $(this).val(59);
      else if($(this).val() < 0) $(this).val(0);
      else if($(this).val() == "") $(this).val(0);
      if(!editMode) setTimeUpload();
      else			setTimeUpdate();
    });
    $('.time-input-h').click(function(){
      if($(this).val() > 10) $(this).val(10);
      else if($(this).val() < 0) $(this).val(0);
      else if($(this).val() == "") $(this).val(0);
      if(!editMode) setTimeUpload();
      else			setTimeUpdate();
    });
}

function setTimeFieldValue(){
	var sh = Math.floor(updateStart/3600);
	var sm = Math.floor((updateStart - (sh*3600))/60);
	var ss = (updateStart - (sh*3600) - (sm*60));
	$('#update-start-h').val(sh);
	$('#update-start-m').val(sm);
	$('#update-start-s').val(ss);


	var eh = Math.floor(updateEnd/3600);
	var em = Math.floor((updateEnd - (eh*3600))/60);
	var es = (updateEnd - (eh*3600) - (em*60));
	$('#update-end-h').val(eh);
	$('#update-end-m').val(em);
	$('#update-end-s').val(es);
}

function checkOverlap(s,e,id){
	var a = audioList;
	if(a.length > 1){
		if(e <= a[0].start && id !== a[0].id) { console.log("c1 : add to first (not self)"); return false; }
		else if(s >= a[a.length-1].end && id !== a[a.length-1].id) { console.log("c2 : add to last (not self)"); return false;}
		for(i = 0; i < a.length-1; i++){
			if(id !== a[i].id){
				if(s >= a[i].end && e <= a[i+1].start && id !== a[i].id) {console.log("c3 : add between after first to before last (not self)"); return false; }
			}
		}
		for(i = 0; i < a.length; i++){
			if(id == a[i].id){
				if(i == 0 && e <= a[i+1].start) {console.log("c6 : add itself to first"); return false; }
				else if(i == a.length-1 && s >= a[i-1].end) {console.log("c6 : add itself to last"); return false; }
				else if(s >= a[i-1].end && e <= a[i+1].start) {console.log("c7 : add itself between after & before it"); return false; }
			}
		}
		console.log("c5 : overlap");
		return true;
	} else {
		console.log("c8 : no audio or only 1 file");
		return false;
	}
}