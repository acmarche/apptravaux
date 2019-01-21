<?php

namespace AcMarche\Avaloir\Form\DataTransformer;

use AcMarche\Avaloir\Entity\Avaloir;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class AvaloirToNumberTransformer implements DataTransformerInterface
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
     * Transforms an object (avaloir) to a string (number).
     *
     * @param  Avaloir|null $avaloir
     * @return string
     */
    public function transform($avaloir)
    {
        if (null === $avaloir) {
            return "";
        }
        if ($avaloir instanceof Avaloir) {
            return $avaloir->getId();
        }

        //me retourne l'id ??
        return $avaloir;
    }

    /**
     * Transforms a string (number) to an object (avaloir).
     *
     * @param  string $number
     *
     * @return Avaloir|null
     *
     * @throws TransformationFailedException if object (avaloir) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $avaloir = $this->om
            ->getRepository(Avaloir::class)
            ->find($number);

        if (null === $avaloir) {
            throw new TransformationFailedException(sprintf(
                'An avaloir with number "%s" does not exist!',
                $number
            ));
        }

        return $avaloir;
    }
}
