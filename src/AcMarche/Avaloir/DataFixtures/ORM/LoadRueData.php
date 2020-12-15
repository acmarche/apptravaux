<?php

namespace AcMarche\Avaloir\DataFixtures\ORM;

use AcMarche\Avaloir\Entity\Village;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use AcMarche\Avaloir\Entity\Rue;
use Doctrine\Persistence\ObjectManager;

class LoadRueData extends Fixture implements ORMFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $villages = array("Aye", "Champlon", "Grimbiémont", "Hargimont", "Hollogne", "Humain",
            "Lignières", "Marche", "Marloie", "On", "Roy", "Verdenne", "Waha");

        foreach ($villages as $value) {
            $village = new Village();
            $village->setNom($value);
            $manager->persist($village);
            $this->addReference($value, $village);
        }

        $rue = new Rue();
        $rue->setNom("Chemin des Lucioles");
        $rue->setVillage($this->getReference('Aye'));
        $rue->setCode(123);

        $manager->persist($rue);

        $rue = new Rue();
        $rue->setNom("Rue des Jolis Bois");
        $rue->setVillage($this->getReference('Aye'));
        $rue->setCode(123);

        $manager->persist($rue);

        $rue = new Rue();
        $rue->setNom("Rue Victor Libert");
        $rue->setVillage($this->getReference('Marche'));
        $rue->setCode(123);

        $manager->persist($rue);

        $rue = new Rue();
        $rue->setNom("Rue des Forgerons");
        $rue->setVillage($this->getReference('On'));
        $rue->setCode(123);

        $manager->persist($rue);

        $manager->flush();
    }
}
