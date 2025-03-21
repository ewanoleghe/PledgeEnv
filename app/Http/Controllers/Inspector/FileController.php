<?php

namespace App\Http\Controllers\Inspector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    // Serve the license file
    public function showLicense($filename)
    {
        // Corrected the path to remove the duplicate "storage" directory
        $path = storage_path("app/private/license/{$filename}");

        if (!file_exists($path)) {
            abort(404); // Or you can return a default image
        }

        return response()->file($path);
    }

    // Serve the signature file
    public function showSignature($filename)
    {
        // Corrected the path to remove the duplicate "storage" directory
        $path = storage_path("app/private/signature/{$filename}");

        if (!file_exists($path)) {
            abort(404); // Or you can return a default image
        }

        return response()->file($path);
    }
}
