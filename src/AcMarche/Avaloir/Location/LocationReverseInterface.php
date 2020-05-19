<?php


namespace AcMarche\Avaloir\Location;


interface LocationReverseInterface
{
    /**
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function reverse($latitude, $longitude): array;

    public function getRoad(): string ;

    public function getLocality( ): string ;

}
