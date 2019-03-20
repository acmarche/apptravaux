<?php


namespace AcMarche\Api\Controller;

use AcMarche\Api\Service\SerializeApi;
use AcMarche\Stock\Repository\CategorieRepository;
use AcMarche\Stock\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Api\Controller
 * @Route("/api")
 */
class DefaultController extends AbstractController
{
    /**
     * @var ProduitRepository
     */
    private $produitRepository;
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;
    /**
     * @var SerializeApi
     */
    private $serializeApi;

    public function __construct(ProduitRepository $produitRepository, CategorieRepository $categorieRepository, SerializeApi $serializeApi)
    {
        $this->produitRepository = $produitRepository;
        $this->categorieRepository = $categorieRepository;
        $this->serializeApi = $serializeApi;
    }

    /**
     * @Route("/all")
     */
    public function index()
    {
        $produits = $this->serializeApi->serializeProduits($this->produitRepository->findAll());
        $categories = $this->serializeApi->serializeCategorie($this->categorieRepository->findAll());

        $data = ['categories' => $categories, 'produits' => $produits];

        return new JsonResponse($data);
    }
}
