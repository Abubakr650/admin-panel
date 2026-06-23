<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{
    /**
     * The disk to use for storage.
     */
    protected string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default', 's3');
    }

    /**
     * Upload a file to the specified directory.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $filenamePrefix
     * @return string
     */
    public function upload(UploadedFile $file, string $folder, ?string $filenamePrefix = null): string
    {
        $prefix = $filenamePrefix ? $filenamePrefix . '_' : '';
        $filename = $prefix . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        return $file->storeAs($folder, $filename, $this->disk);
    }

    /**
     * Delete a file from the disk.
     *
     * @param string|null $path
     * @return bool
     */
    public function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }

    /**
     * Get the URL for a stored file.
     *
     * @param string|null $path
     * @return string|null
     */
    public function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Storage::disk($this->disk)->url($path);
    }
}
