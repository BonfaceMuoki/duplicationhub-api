<?php

namespace App\Http\Controllers;
use App\Http\Services\FileUploadService;

class UtilityController extends Controller
{

    protected $fileUploadService;
    public function __construct(FileUploadService $fileUploadService)
    {
     
        $this->fileUploadService=$fileUploadService;
    }

   
  
}
