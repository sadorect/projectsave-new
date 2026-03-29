<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface ScansUploadedFiles
{
    public function scan(UploadedFile $file, string $attribute = 'file'): void;
}
