<?php

namespace AcMarche\Travaux\Tests\Search;

use AcMarche\Travaux\Tests\Controller\BaseUnit;

class DefaultControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertContains('Documentation', $this->admin->getResponse()->getContent());
        // print_r($this->admin->getResponse()->getContent());

        $date = new \DateTime();

        $form = $crawler->selectButton('Rechercher')->form(array(
            'search_intervention[intitule]' => 'parking',
            'search_intervention[id]' => 345,
            'search_intervention[date_debut]' => $date->format('Y/m/d'),
            'search_intervention[date_fin]' => $date->format('Y/m/d'),
        ));

        $priorite_option = $crawler->filter('#search_intervention_priorite option:contains("Normal")');
        $this->assertGreaterThan(0, count($priorite_option), 'Normal non trouvé');
        $priorite = $priorite_option->attr('value');
        $form['search_intervention[priorite]']->select($priorite);

        $domaine_option = $crawler->filter('#search_intervention_domaine option:contains("Parc et jardin")');
        $this->assertGreaterThan(0, count($domaine_option), 'Parc et jardin non trouvé');
        $domaine = $domaine_option->attr('value');
        $form['search_intervention[domaine]']->select($domaine);

        $categorie_option = $crawler->filter('#search_intervention_categorie option:contains("Intervention")');
        $this->assertGreaterThan(0, count($categorie_option), 'Intervention non trouvé');
        $categorie = $categorie_option->attr('value');
        $form['search_intervention[categorie]']->select($categorie);

        $batiment_option = $crawler->filter('#search_intervention_batiment option:contains("Ecole de Aye")');
        $this->assertGreaterThan(0, count($batiment_option), 'Ecole de Aye non trouvé');
        $batiment = $batiment_option->attr('value');
        $form['search_intervention[batiment]']->select($batiment);

        $etat_option = $crawler->filter('#search_intervention_etat option:contains("Nouveau")');
        $this->assertGreaterThan(0, count($etat_option), 'Nouveau non trouvé');
        $etat = $etat_option->attr('value');
        $form['search_intervention[etat]']->select($etat);

        $this->admin->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Réorganiser le parking")')->count());
    }
}
