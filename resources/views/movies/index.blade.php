@extends('fileupload')

@section('content')
	<h2>Movie Upload</h2>
	<div class = "well">
	   <div class="form-cover-movie">
		   	<form id="upload-movie-form" action="/upload-movie">
		   		<div class="form-group">
		   			<div class="form-group">
			          <label for="movie_name">Name</label><br>
			          <input type="text" class="form-control" name="name" id="movie_name" autocomplete="off">
			        </div>
			        <div class="form-group">
				        <label for="movie_attachment">Link File</label><br>
				        <input type="text" class="form-control input-file-text" name="path" id="movie_attachment">
				    </div>
				    <div class="form-group">
				    <label class="label-hidden" for="movie_upload">Upload</label><br>
					    <button onclick="uploadMovie();" class="btn btn-primary">Upload</button>
					</div>
		   		</div>
		   	</form>
	    </div>
	</div>
	<div class="container movie-container">
	  <table class = "table table-bordered table-striped" style="background:#f5f5f5;">
	     <thead>
	        <tr>
	           <th class="col-md-1 text-center">No</th>
	           <th class="col-md-5 text-center">Name</th>
	           <th class="col-md-1-5 text-center">Created / Updated</th>
	           <th class="col-md-3 text-center">Action</th>
	        </tr>
	     </thead>
	     <?php $x = 0 ?>
	     <tbody>
	     	@foreach($movies as $movie)
	           <tr>
	          	  <td class="col-md-1">{{ $x = $x + 1 }}</td>
	              <td class="col-md-5">{{ $movie->name }}</td>
	              <td class="col-md-1-5">{{ $movie->created_at }}<br>{{ $movie->updated_at }}</td>
	              <td class="col-md-3 text-center">
	              	<form id="del-{{ $movie->id }}" action="/delete-movie/{{ $movie->id }}" method="GET"></form>
	              	<button class="btn btn-primary" onclick="updateMovie('{{ $movie->id }}','{{$movie->name}}','{{$movie->file}}');">EDIT</button>         
	              	<a href="/movie/{{ $movie->id }}">
	              		<button class="btn btn-success">AD</button>
	              	</a>
	              	<button class="btn btn-danger" onclick="deleteRecord('{{ $movie->id }}');">DELETE</button>
	              </td>
	           </tr>
	        @endforeach
	     </tbody>
	     
	  </table>
	</div>
	<div class="popup">
		<div class="box">
			<div class="confirm-box">
				<h3>Comfirm to delete this movie</h3>
				<button class="btn btn-success" onclick="confirmDelete();">YES</button>
				<button class="btn btn-danger" onclick="popOut();">NO</button>
			</div>
		</div>
	</div>
	<div class="update-popup">
		<div class="update-box">
			<form class="confirm-box" id="update-box" action="/update-movie">
				<h3>Update Movie Infomation</h3>
				<input type="hidden" class="form-control" name="id" id="update_id" autocomplete="off">
				<label class="col-md-2">Name</label>
				<div class="col-md-10">
					<input type="text" class="form-control" name="name" id="update_name" autocomplete="off">
				</div>
				<label class="col-md-2">Link File</label>
				<div class="col-md-10">
					<input type="text" class="form-control" name="path" id="update_path" autocomplete="off">
			    </div>
				<button class="btn btn-success" id="movie-update-confirm" onclick="confirmUpdate();">SAVE</button>
				<button class="btn btn-danger" onclick="popOut();">CANCEL</button>
			</form>
		</div>
	</div>
	<script type="text/javascript"> 
		var type = 'movie'; 
		var audioList = 0;
	</script>
@stop