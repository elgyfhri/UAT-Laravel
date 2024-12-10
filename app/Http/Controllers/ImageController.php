<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class ImageController extends Controller
{
    public function serveImage($path)
    {
        $filePath = public_path('images/notes/' . $path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return Response::make(file_get_contents($filePath), 200, [
            'Content-Type' => mime_content_type($filePath),
            'Access-Control-Allow-Origin' => 'http://localhost:5173',
        ]);
    }
}
