<?php

namespace App\Traits;


use InvalidArgumentException;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;



trait HasImageUpload
{

    public function storeImage(UploadedFile $image, string $type = 'movies'): string
    {

        $validTypes = ['movies', 'series', 'users','attachments'];
        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException('Invalid image type. Allowed types are: ' . implode(', ', $validTypes));
        }

        $fileName =uniqid('img_', true)  . '.' . $image->getClientOriginalExtension();

        // Resize or compress using Intervention Image (optional)
        $manager = new ImageManager(new Driver());
        $img = $manager->read($image);

        $imagePath = $type . '/' . $fileName;

        $directoryPath = storage_path('app/public/' . $type);
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0775, true);  
        }
        
        // Save optimized image to storage/app/public/{type}
        $absolutePath = storage_path('app/public/' . $imagePath);
        $img->toJpeg(75)->save($absolutePath);

        return 'storage/' . $imagePath;
    }

    /**
     * Delete an image from storage.
     *
     * @param string $imagePath The path to the image in storage
     * @return void
     */
    public function deleteImage(string $imagePath): void
    {

        $pathInStorage = str_replace('storage/', '', $imagePath);

        if (Storage::disk('public')->exists($pathInStorage)) {
            Storage::disk('public')->delete($pathInStorage);
        }
    }
}
