<?php

namespace App\Traits;


use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;



trait HasImageUpload
{

    public function storeImage(UploadedFile $image, string $type = 'movies'):string
    {
       
        if (!in_array($type, ['movies', 'series'])) {
            return false;
        }

        $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
        $manager = new ImageManager(new Driver());
        $img = $manager->read($image);
        $basePath = 'public/' . $type . '/' . $fileName ;
        $directoryPath = base_path('public/' . $type);
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true); 
        }
        $image->move(base_path('public/' . $type), $fileName);
        $img->toJpeg(75)->save(base_path($basePath));
        return (string) $basePath ;
    }

    /**
     * Delete an image from storage.
     *
     * @param string $imagePath The path to the image in storage
     * @return void
     */
    public function deleteImage(string $imagePath): void
    {
        // Check if the image exists and delete it from the public disk
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
