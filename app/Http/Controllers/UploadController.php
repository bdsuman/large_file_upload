<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $uuid = $request->input('identifier');
        $index = $request->input('chunkIndex');
        $total = $request->input('totalChunks');
        $name = $request->input('name');
    
        if (!$uuid || $index === null || !$total || !$name || !$request->hasFile('file')) {
            return response()->json(['error' => 'Missing upload data'], 400);
        }
    
        $chunk = $request->file('file');
    
        $chunkDir = storage_path("app/chunks/{$uuid}");
        if (!file_exists($chunkDir)) {
            mkdir($chunkDir, 0777, true);
        }
    
        $chunk->move($chunkDir, "chunk_{$index}");
    
        if ((int)$index + 1 === (int)$total) {
            $finalPath = storage_path("app/uploads/{$name}");
            $out = fopen($finalPath, 'ab');
    
            for ($i = 0; $i < $total; $i++) {
                $chunkPath = "{$chunkDir}/chunk_{$i}";
                if (!file_exists($chunkPath)) {
                    return response()->json(['error' => "Missing chunk {$i}"], 400);
                }
                $in = fopen($chunkPath, 'rb');
                stream_copy_to_stream($in, $out);
                fclose($in);
                unlink($chunkPath);
            }
    
            fclose($out);
            rmdir($chunkDir);
    
            return response()->json(['success' => true, 'file' => $name]);
        }
    
        return response()->json(['chunk_saved' => true]);
    }
    

    public function delete(Request $request)
    {
        $file = $request->input('file');
        $path = storage_path("app/uploads/" . basename($file));

        if (file_exists($path)) {
            unlink($path);
            return response()->json(['deleted' => true]);
        }

        return response()->json(['error' => 'File not found'], 404);
    }

    public function revert()
    {
        return response()->json(['reverted' => true]);
    }
}
