<?php

namespace App\Http\Controllers;

use Request;

use App\Http\Requests;

use App\audio;
use App\movie;

class AudioController extends Controller
{
    public function index($id){
        $movie = Movie::where('id','=',$id)->first();
        $audios = Audio::all()->where('movie_id','=',$id);
        return view('audios.index')->with('audios',$audios)
                                   ->with('movie',$movie);
    }

    public function getAudios(){
        $movie_id = Request::query('movie_id');
        $audios = Audio::all()->where('movie_id','=',$movie_id);
        header('Content-Type: application/json');
        echo json_encode($audios);
    }

    public function recorder(){
        return view('recorder');
    }

    public function delete(){
        $movie_id = Request::query('movie_id');
        $id = Request::query('id');
        $del = Audio::where('id','=',$id)->delete();
        $audios = Audio::all()->where('movie_id','=',$movie_id);
        header('Content-Type: application/json');
        echo json_encode($audios);
    }

    public function update(){
        $movie_id = Request::query('movie_id');
        $id = Request::query('id');
        $name = Request::query('name');
        $path = Request::query('path');
        $start = Request::query('start');
        $end = Request::query('end');
        $audio = Audio::where('id','=',$id);
        $audio->update(['name' => $name, 'file' => $path, 'start' => $start, 'end' => $end]);
        $audios = Audio::all()->where('movie_id','=',$movie_id);
        header('Content-Type: application/json');
        echo json_encode($audios);
    }

    public function uploadAudio(){
        $movie_id = Request::query('movie_id');
        $audio = new Audio;
        $audio->movie_id = $movie_id;
        $audio->movie_name = Movie::where('id','=',$movie_id)->first()->name;
        $audio->name = Request::query('name');
        $audio->file = Request::query('path');
        $audio->start = Request::query('start');
        $audio->end = Request::query('end');
        $audio->save();

        $audios = Audio::all()->where('movie_id','=',$movie_id);
        header('Content-Type: application/json');
        echo json_encode($audios);
    }

    public function genAudios(){
        for($i = 0; $i < 10; $i++){
            $audio = new Audio;
            $audio->movie_id = $i;
            $audio->movie_name = "Mov-".$i;
            $audio->name = "Aud-0".$i;
            $audio->file = "Path-0".$i;
            $audio->start = $i;
            $audio->end = $i+100;
            $audio->save();
        }
        return 'done';
    }

    public function showData(){
        return Audio::all();
    }
}
