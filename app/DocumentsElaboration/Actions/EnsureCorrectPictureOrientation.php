<?php

namespace KBox\DocumentsElaboration\Actions;

use KBox\Contracts\Action;
use Intervention\Image\Facades\Image as ImageFacade;

/**
 * Ensure that a JPEG is properly rotated according
 * to the image orientation Exif tag
 */
class EnsureCorrectPictureOrientation extends Action
{
    protected $canFail = true;

    public function run($descriptor)
    {
        $picture = $descriptor->file;

        if ($picture && $picture->mime_type === 'image/jpeg') {
            $picture_copy = $picture->copyAsNewVersion();
            
            // Rotate and re-save the image
            $image = ImageFacade::make($picture->absolute_path);

            $image
                ->orientate()
                ->save($picture_copy->absolute_path, config('image.quality'));
            
            $picture_copy->rehash();

            $descriptor->file_id = $picture_copy->id;
            $descriptor->hash = $picture_copy->hash;
            $descriptor->save();
        }
        
        return $descriptor;
    }
}
