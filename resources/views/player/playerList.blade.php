<html>
<head>
	<title>Movie Lists</title>
	<link rel="stylesheet" type="text/css"  href="{!! asset('css/fileupload.css') !!}" />
    <link rel="stylesheet" type="text/css"  href="{!! asset('css/app.css') !!}" />
</head>
	<body>
		<br>
		<h2>Movie Lists</h2>
		<br>
		<div class="container">
		  <table class = "table table-bordered table-striped" style="background:#f5f5f5;">
		     <thead>
		        <tr>
		           <th class="col-md-1 text-center">No</th>
		           <th class="col-md-5 text-center">Name</th>
		           <th class="col-md-3 text-center">Action</th>
		        </tr>
		     </thead>
		     <?php $x = 0 ?>
		     <tbody>
		     	@foreach($movies as $movie)
		           <tr>
		          	  <td class="col-md-1 text-center">{{ $x = $x + 1 }}</td>
		              <td class="col-md-5">{{ $movie->name }}</td>
		              <td class="col-md-3 text-center">
		              	<a href="/player/{{ $movie->id }}"><button class="btn btn-success">Play</button></a>
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
		</script>
	</body>
</html>