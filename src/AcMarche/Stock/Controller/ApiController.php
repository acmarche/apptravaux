<?php

namespace AcMarche\Stock\Controller;

use AcMarche\Stock\Entity\Produit;
use AcMarche\Stock\Repository\CategorieRepository;
use AcMarche\Stock\Repository\ProduitRepository;
use AcMarche\Stock\Service\Logger;
use AcMarche\Stock\Service\SerializeApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Api\Controller
 * @Route("/api")
 */
class ApiController extends AbstractController
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
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        SerializeApi $serializeApi,
        Logger $logger
    ) {
        $this->produitRepository = $produitRepository;
        $this->categorieRepository = $categorieRepository;
        $this->serializeApi = $serializeApi;
        $this->logger = $logger;
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

    /**
     * @param Produit $produit
     * @param int $quantite
     * @Route("/update/{id}/{quantite}")
     * @return JsonResponse
     */
    public function updateQuantite(Produit $produit, int $quantite)
    {
        $produit->setQuantite($quantite);
        $this->produitRepository->flush();
        $data = ['quantite' => $quantite];

        $this->logger->log($produit, $quantite);


        return new JsonResponse($data);
    }
}
