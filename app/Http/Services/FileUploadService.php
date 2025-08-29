<?php
namespace App\Http\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

Class FileUploadService{
    public function validateFile(Request $request){

    }
    
    public function uploadExamPaperTemporarily(Request $request)
    {
        try {
         
            $validatedData = $request->validate([
                'file' => 'required|mimes:pdf|max:2048', 
            ]);

          
            $directory = 'temp/exam_papers';
            $this->ensureDirectoryExists($directory);

           
            $file = $request->file('file');
            $filePath = $file->store($directory, 'public'); 

           
            $fileUrl = asset('storage/' . $filePath);

          
            return response()->json([
                'message' => 'File uploaded successfully.',
                'file_url' => $fileUrl,
            ]);
        } catch (\Exception $e) {
         
            return response()->json([
                'message' => 'An error occurred while uploading the file.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ensure the specified directory exists in the public disk.
     *
     * @param string $directory
     * @return void
     */
    private function ensureDirectoryExists(string $directory): void
    {
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
    }
}