<?php


namespace AcMarche\Avaloir\Image;

use AcMarche\Avaloir\Entity\Avaloir;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class ImageService
 * @package AcMarche\Avaloir\Image
 */
class ImageService
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    public function __construct(ParameterBagInterface $parameterBag, UploaderHelper $uploaderHelper)
    {
        $this->parameterBag = $parameterBag;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * @param $imagePath
     * @param $angle
     * @param $color
     * @return string
     * @throws \ImagickException
     */
    function rotateImage(Avaloir $avaloir)
    {
        if ($this->getOrientationImage($avaloir) == 6) {
            $imagick = new \Imagick($this->getPath($avaloir));
            $color = $imagick->getImageBackgroundColor();
            $imagick->rotateimage($color, 90);
        }

        return $imagick->writeImage($this->getPath($avaloir));
    }

    function getOrientationImage(Avaloir $avaloir)
    {
        $imagick = new \Imagick($this->getPath($avaloir));

        return $imagick->getImageOrientation();
    }

    function getPath(Avaloir $avaloir): string
    {
        return $this->parameterBag->get('ac_marche_travaux_dir_public').$this->uploaderHelper->asset(
                $avaloir,
                'imageFile'
            );
    }
}
