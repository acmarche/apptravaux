<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 20/03/19
 * Time: 14:06
 */

namespace AcMarche\Stock\Service;

use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\Entity\DateNettoyage;
use AcMarche\Stock\Entity\Categorie;
use AcMarche\Stock\Entity\Produit;
use AcMarche\Travaux\Entity\Security\User;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class SerializeApi
{
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var FilterService
     */
    private $filterService;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        UploaderHelper $uploaderHelper,
        RequestStack $requestStack,
        FilterService $filterService,
        ParameterBagInterface $parameterBag
    ) {
        $this->uploaderHelper = $uploaderHelper;
        $this->requestStack = $requestStack;
        $this->filterService = $filterService;
        $this->parameterBag = $parameterBag;
    }

    public function getUrl()
    {
        if ($this->requestStack->getMasterRequest()) {
            return $this->requestStack->getMasterRequest()->getSchemeAndHttpHost();
        }
        return '';
    }

    public function serializeAvaloir(Avaloir $avaloir)
    {
        $std = new \stdClass();
        $std->id = $avaloir->getId();
        $std->idReferent = $avaloir->getId();
        $std->latitude = $avaloir->getLatitude();
        $std->longitude = $avaloir->getLongitude();
        $std->rue = $avaloir->getRue();
        $std->description = $avaloir->getDescription();
        if ($avaloir->getImageName()) {
            $root = $this->parameterBag->get('ac_marche_travaux_dir_public');
            $pathImg = $this->uploaderHelper->asset($avaloir, 'imageFile');
            $fullPath = $root . $pathImg;
            if (is_readable($fullPath)) {
                $thumb = $this->filterService->getUrlOfFilteredImage($pathImg, 'actravaux_thumb');
                if ($thumb) {
                    $std->imageUrl = $thumb;
                } else {
                    $std->imageUrl = $this->getUrl() . $this->uploaderHelper->asset($avaloir, 'imageFile');
                }
            }
        }
        return $std;
    }

    /**
     * @param Avaloir[] $avaloirNews
     * @return array
     */
    public function serializeAvaloirs(iterable $avaloirs)
    {
        $data = [];
        foreach ($avaloirs as $avaloir) {
            $std = $this->serializeAvaloir($avaloir);
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

    /**
     * @param DateNettoyage[] $dates
     * @return array
     */
    public function serializeDates(array $dates)
    {
        $data = [];
        foreach ($dates as $date) {
            $std = $this->serializeDate($date);
            $data[] = $std;
        }

        return $data;
    }

    public function serializeDate(DateNettoyage $date)
    {
        $std = new \stdClass();
        $std->id = $date->getId();
        $std->avaloirId = $date->getAvaloir()->getId();
        $std->date = $date->getJour()->format('Y-m-d');

        return $std;
    }
}