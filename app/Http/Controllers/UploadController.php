<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'files'   => 'required|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,gif,webp,pdf,docx|max:20240', // 10MB لكل ملف
        ]);

        $files = $request->file('files');
        if (!is_array($files)) {
            $files = [$files];
        }

        $paths = [];

        foreach ($files as $file) {
            $ext = strtolower($file->getClientOriginalExtension());

            // اختيار المجلد المناسب
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $folder = 'uploads/images';
            } elseif ($ext === 'pdf') {
                $folder = 'uploads/docs';
            }elseif($ext === 'docx'){
                $folder = 'uploads/word';
            } else {
                $folder = 'uploads/others';
            }

            $filename = Str::uuid() . '.' . $ext;
            $path = $file->storeAs($folder, $filename, 'public');
            $paths[] = asset('storage/' . $path);
        }

        return response()->json([
            'status' => true,
            'message' => 'Files uploaded successfully',
            'files' => $paths
        ], 201);
    }
}
