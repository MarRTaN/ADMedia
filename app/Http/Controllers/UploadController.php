<?php

namespace App\Http\Controllers;

use Request;

use App\Http\Requests;

use App\audio;
use App\movie;

class UploadController extends Controller
{
    public function index(){
        return view('recordupload');
    }

    //NOTE $target_path = $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . basename($_FILES['uploadedFile']['name']);

    public function uploadRecord(){
        //$target_dir = dirname(__DIR__) . "/Upload/";
        $target_dir = $_SERVER['DOCUMENT_ROOT']."/upload/";
        $message = 'Error uploading file';
        switch( $_FILES['file']['error'] ) {
            case UPLOAD_ERR_OK:
                $message = false;;
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $message .= ' - file too large (limit of bytes).';
                break;
            case UPLOAD_ERR_PARTIAL:
                $message .= ' - file upload was not completed.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $message .= ' - zero-length file uploaded.';
                break;
            default:
                $message .= ' - internal error #'.$_FILES['file']['error'];
                break;
        }
        if( !$message ) {
            if( !is_uploaded_file($_FILES['file']['tmp_name']) ) {
                $message = 'Error uploading file - unknown error.';
            } else {
                // Let's see if we can move the file...
                $t = time();
                $uploadname = "a".$t."ud" . ".ogg";
                $dest = $target_dir . $uploadname;
                if( !move_uploaded_file($_FILES['file']['tmp_name'], $dest) ) { // No error supporession so we can see the underlying error.
                    $message = 'Error uploading file - could not save upload (this will probably be a permissions problem in '.$dest.')';
                } else {
                    $message = 'File uploaded okay.';
                    return $uploadname;
                }
            }
        }

        return 'Error:'.$message;
        //if(isset($_FILES['file']) and !$_FILES['file']['error']){
            //$fname = "11" . ".ogg";
            //move_uploaded_file($_FILES['file']['tmp_name'], $target_dir.$fname);
        //}
        //return redirect('/movie');
    }
}
