<?php

namespace AcMarche\Travaux\DataFixtures\ORM;

use AcMarche\Travaux\Entity\Batiment;
use AcMarche\Travaux\Entity\Categorie;
use AcMarche\Travaux\Entity\Domaine;
use AcMarche\Travaux\Entity\Etat;
use AcMarche\Travaux\Entity\Priorite;
use AcMarche\Travaux\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadData extends Fixture implements DependentFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $etats = array('Nouveau' => 'Nouveau', 'En cours' => 'En cours', 'En attente' => 'En attente');
        foreach ($etats as $value) {
            $etat = new Etat();
            $etat->setIntitule($value);
            $manager->persist($etat);
            $this->addReference($value, $etat);
        }

        $priorites = array('Normal' => 'Normal', 'Faible' => 'Faible', 'Haute' => 'Haute');

        foreach ($priorites as $value) {
            $priorite = new Priorite();
            $priorite->setIntitule($value);
            $manager->persist($priorite);
            $this->addReference($value, $priorite);
        }

        $batiment = new Batiment();
        $batiment->setIntitule('Ecole de Aye');
        $batiment->setSlugname("ecole-aye");
        $batiment->setCreated(new \DateTime());
        $batiment->setUpdated(new \DateTime());
        $manager->persist($batiment);
        $this->addReference('ecole-aye', $batiment);

        $batiment = new Batiment();
        $batiment->setIntitule("Ecole de Hollogne");
        $batiment->setSlugname("ecole-hollogne");
        $batiment->setCreated(new \DateTime());
        $batiment->setUpdated(new \DateTime());
        $manager->persist($batiment);
        $this->addReference('ecole-hollogne', $batiment);

        $categorie = new Categorie();
        $categorie->setIntitule("Intervention");
        $categorie->setSlugname("intervention");
        $categorie->setCreated(new \DateTime());
        $categorie->setUpdated(new \DateTime());
        $manager->persist($categorie);
        $this->addReference('cat-intervention', $categorie);

        $domaine = new Domaine();
        $domaine->setIntitule("Parc et jardin");
        $domaine->setSlugname("parc-jardin");
        $domaine->setCreated(new \DateTime());
        $domaine->setUpdated(new \DateTime());
        $manager->persist($domaine);
        $this->addReference('parc-jardin', $domaine);

        $service = new Service();
        $service->setIntitule("Enseignement");
        $service->setSlugname("enseignement");
        $service->setCreated(new \DateTime());
        $service->setUpdated(new \DateTime());
        $manager->persist($service);
        $this->addReference('enseignement', $service);

        $manager->flush();
    }


    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [LoadUsers::class];
    }
}
