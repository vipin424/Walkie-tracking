<?php
namespace App\Traits;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

trait ImageCompress
{

    public function compressAndStore($file)
    {
        $manager = new ImageManager(new Driver());

        // Read image
        $image = $manager->read($file->getRealPath());

        // Resize (keep aspect ratio)
        $image = $image->scale(width: 350);

        // Encode image properly (VERY IMPORTANT)
        $encoded = $image->toJpeg(75); // quality 75 (recommended)

        $path = 'aadhaar/' . uniqid() . '.jpg';

        Storage::disk('public')->put($path, $encoded);

        return $path;
    }
}
