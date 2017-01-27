<?php

namespace App\Http\Controllers;

use Request;

use App\Http\Requests;

use App\movie;
use App\audio;

class MovieController extends Controller
{
    //
    public function index(){
    	$movies = Movie::all();
    	return view('movies.index', compact('movies'));
    }

    public function delete($id){
    	$del = Movie::where('id','=',$id)->delete();
        $delau = Audio::where('movie_id','=',$id)->delete();
    	return redirect('/movie');
    }

    public function update(){
        $id = Request::query('id');
        $movie = Movie::where('id','=',$id);
        $movie->update(['name' => Request::query('name'), 'file' => Request::query('path')]);
        return redirect('/movie');
    }

    public function uploadMovie(){
        $movie = new Movie;
        $movie->name = Request::query('name');
        $movie->file = Request::query('path');
        $movie->save();
        return redirect('/movie');
    }
}
