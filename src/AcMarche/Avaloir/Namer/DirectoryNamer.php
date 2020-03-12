<?php


namespace AcMarche\Avaloir\Namer;


use AcMarche\Avaloir\Entity\AvaloirNew;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class DirectoryNamer implements DirectoryNamerInterface
{
    /**
     * @inheritDoc
     * @param AvaloirNew $object
     */
    public function directoryName($object, PropertyMapping $mapping): string
    {
        return $object->getId();
    }
}