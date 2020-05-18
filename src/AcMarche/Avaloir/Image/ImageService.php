<?php


namespace AcMarche\Avaloir\Image;


class ImageService
{
    /**
     * @param $imagePath
     * @param $angle
     * @param $color
     * @return string
     * @throws \ImagickException
     */
    function rotateImage($imagePath)
    {
        $imagick = new \Imagick(realpath($imagePath));
        var_dump($imagick->getImageOrientation());
        $color = $imagick->getImageBackgroundColor();
        if ($imagick->getImageOrientation() == 6) {
            $imagick->rotateimage($color, 90);
        }

        return $imagick->writeImage('/home/jfsenechal/Bureau/aval-36.jpg');
    }
}
