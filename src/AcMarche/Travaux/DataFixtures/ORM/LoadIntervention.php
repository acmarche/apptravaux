<?php

namespace AcMarche\Travaux\DataFixtures\ORM;

use AcMarche\Travaux\Entity\Intervention;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIntervention extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $intervention = new Intervention();
        $intervention->setIntitule('Arbres à Hollogne a retailler');
        $intervention->setBatiment($this->getReference('ecole-hollogne'));
        $intervention->setCategorie($this->getReference('cat-intervention'));
        $intervention->setDescriptif('Les branches gênent le passage');
        $intervention->setDomaine($this->getReference('parc-jardin'));
        $intervention->setEtat($this->getReference('Nouveau'));
        $intervention->setPriorite($this->getReference('Normal'));
        $intervention->setService($this->getReference('enseignement'));
        $intervention->setUserAdd('contributeur');
        $intervention->setCreated(new \DateTime());
        $intervention->setUpdated(new \DateTime());
        $intervention->setCurrentPlace(['auteur_checking' => 1]);

        $manager->persist($intervention);

        $intervention = new Intervention();
        $intervention->setIntitule('Nettoyer les cours des écoles');
        $intervention->setBatiment($this->getReference('ecole-aye'));
        $intervention->setCategorie($this->getReference('cat-intervention'));
        $intervention->setDescriptif('Elles sont vraiment très sales');
        $intervention->setDomaine($this->getReference('parc-jardin'));
        $intervention->setEtat($this->getReference('Nouveau'));
        $intervention->setPriorite($this->getReference('Normal'));
        $intervention->setService($this->getReference('enseignement'));
        $intervention->setUserAdd('auteur');
        $intervention->setCreated(new \DateTime());
        $intervention->setUpdated(new \DateTime());
        $intervention->setCurrentPlace(['admin_checking' => 1]);
        $manager->persist($intervention);

        $intervention = new Intervention();
        $intervention->setIntitule('Repeindre les chassis');
        $intervention->setBatiment($this->getReference('ecole-aye'));
        $intervention->setCategorie($this->getReference('cat-intervention'));
        $intervention->setDescriptif('Elles sont vraiment très sales');
        $intervention->setDomaine($this->getReference('parc-jardin'));
        $intervention->setEtat($this->getReference('Nouveau'));
        $intervention->setPriorite($this->getReference('Normal'));
        $intervention->setService($this->getReference('enseignement'));
        $intervention->setUserAdd('auteur');
        $intervention->setCreated(new \DateTime());
        $intervention->setUpdated(new \DateTime());
        $intervention->setCurrentPlace(['admin_checking' => 1]);
        $manager->persist($intervention);

        $intervention = new Intervention();
        $intervention->setIntitule('Réorganiser le parking');
        $intervention->setBatiment($this->getReference('ecole-aye'));
        $intervention->setCategorie($this->getReference('cat-intervention'));
        $intervention->setDescriptif('Elles sont vraiment très sales');
        $intervention->setDomaine($this->getReference('parc-jardin'));
        $intervention->setEtat($this->getReference('Nouveau'));
        $intervention->setPriorite($this->getReference('Normal'));
        $intervention->setService($this->getReference('enseignement'));
        $intervention->setUserAdd('auteur');
        $intervention->setCreated(new \DateTime());
        $intervention->setUpdated(new \DateTime());
        $intervention->setCurrentPlace(['published' => 1]);
        $manager->persist($intervention);

        $intervention = new Intervention();
        $intervention->setIntitule('Attribuer prive');
        $intervention->setAffectePrive(true);
        $intervention->setBatiment($this->getReference('ecole-aye'));
        $intervention->setCategorie($this->getReference('cat-intervention'));
        $intervention->setDescriptif('Pour une entreprise externe');
        $intervention->setDomaine($this->getReference('parc-jardin'));
        $intervention->setEtat($this->getReference('Nouveau'));
        $intervention->setPriorite($this->getReference('Normal'));
        $intervention->setService($this->getReference('enseignement'));
        $intervention->setUserAdd('redacteur');
        $intervention->setCreated(new \DateTime());
        $intervention->setUpdated(new \DateTime());
        $intervention->setCurrentPlace(['published' => 1]);
        $manager->persist($intervention);

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
        return [LoadUsers::class, LoadData::class];
    }
}
