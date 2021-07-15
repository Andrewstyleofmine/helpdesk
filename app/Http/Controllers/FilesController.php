<?php

namespace App\Http\Controllers;

use App\Files;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    public function getDownload($id)
    {
        $file = Files::find($id);
        $headers = [
            'Content-Type' => 'application/jpeg',
        ];

        return response()->download(public_path() . $file->path, 'img.jpeg', $headers);
    }
}
