<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class audio extends Model
{
    //
    public $table = "audios";
    public function movie() {
        return $this->hasOne('movie'); // this matches the Eloquent model
    }
}
