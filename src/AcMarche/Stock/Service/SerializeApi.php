<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 20/03/19
 * Time: 14:06
 */

namespace AcMarche\Stock\Service;

use AcMarche\Avaloir\Entity\AvaloirNew;
use AcMarche\Stock\Entity\Categorie;
use AcMarche\Stock\Entity\Produit;
use AcMarche\Travaux\Entity\Security\User;

class SerializeApi
{
    /**
     * @param AvaloirNew[] $avaloirNews
     * @return array
     */
    public function serializeAvaloirs(iterable $avaloirs)
    {
        $data = [];
        foreach ($avaloirs as $avaloir) {
            $std = new \stdClass();
            $std->id = $avaloir->getId();
            $std->idReferent = $avaloir->getId();
            $std->latitude = $avaloir->getLatitude();
            $std->longitude = $avaloir->getLongitude();
            $std->rue = $avaloir->getRue();
            $std->description = $avaloir->getDescription();
            $data[] = $std;
        }

        return $data;
    }

    /**
     * @param Produit[] $produits
     * @return array
     */
    public function serializeProduits(iterable $produits)
    {
        $data = [];
        foreach ($produits as $produit) {
            $std = new \stdClass();
            $std->id = $produit->getId();
            $std->nom = $produit->getNom();
            $std->categorie_id = $produit->getCategorie()->getId();
            $std->description = $produit->getDescription();
            $std->quantite = $produit->getQuantite();
            $std->reference = $produit->getReference();
            $std->image = '';
            $data[] = $std;
        }

        return $data;
    }

    /**
     * @param Categorie[] $categories
     * @return array
     */
    public function serializeCategorie(iterable $categories)
    {
        $data = [];
        foreach ($categories as $categorie) {
            $std = new \stdClass();
            $std->id = $categorie->getId();
            $std->nom = $categorie->getNom();
            $std->description = $categorie->getDescription();
            $data[] = $std;
        }

        return $data;
    }

    /**
     * @param User $user
     * @return \stdClass
     */
    public function serializeUser(User $user)
    {
        $token = "123456";
        $std = new \stdClass();
        $std->id = $user->getId();
        $std->nom = $user->getNom();
        $std->prenom = $user->getPrenom();
        $std->email = $user->getEmail();
        $std->token = $token;

        $user->setToken($token);

        return $std;
    }
}