<?php

namespace AcMarche\Avaloir\Form\DataTransformer;

use AcMarche\Avaloir\Entity\Quartier;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class QuartierToNumberTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (quartier) to a string (number).
     *
     * @param  Quartier|null $quartier
     * @return string
     */
    public function transform($quartier)
    {
        if (null === $quartier) {
            return "";
        }
        if ($quartier instanceof Quartier) {
            return $quartier->getId();
        }

        //me retourne l'id ??
        return $quartier;
    }

    /**
     * Transforms a string (number) to an object (quartier).
     *
     * @param  string $number
     *
     * @return Quartier|null
     *
     * @throws TransformationFailedException if object (quartier) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $quartier = $this->om
            ->getRepository(Quartier::class)
            ->find($number);

        if (null === $quartier) {
            throw new TransformationFailedException(sprintf(
                'An quartier with number "%s" does not exist!',
                $number
            ));
        }

        return $quartier;
    }
}
