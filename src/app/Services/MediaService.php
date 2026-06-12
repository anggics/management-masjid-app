<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Pembungkus penyimpanan media. Default: disk lokal "public".
 * Bila CLOUDINARY_URL diisi, dapat dikembangkan untuk upload ke Cloudinary.
 */
class MediaService
{
    public function store(UploadedFile $file, string $folder = 'uploads'): string
    {
        $name = Str::ulid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $name, 'public');

        return Storage::disk('public')->url($path);
    }
}
