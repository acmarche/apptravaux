<?php

namespace AcMarche\Avaloir\Twig;

use AcMarche\Avaloir\Location\StreetView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AvaloirExtension extends AbstractExtension
{
    /**
     * @var StreetView
     */
    private $streetView;

    public function __construct(StreetView $streetView)
    {
        $this->streetView = $streetView;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('street_view', [$this, 'StreetView']),
        ];
    }

    /**
     * @param string $latitude
     * @param string $longitude
     * @return array|mixed|string
     */
    public function StreetView(string $latitude, string $longitude)
    {
        $content = $this->streetView->getPhoto($latitude, $longitude);
        try {
            $img = json_decode($content, true);
            if (is_array($img) && $img['error']) {
                return $img['message'];
            }
        } catch (\Exception $exception) {
        }

        return base64_encode($content);
    }
}
