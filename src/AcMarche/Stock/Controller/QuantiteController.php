<?php


namespace AcMarche\Stock\Controller;

use AcMarche\Stock\Entity\Produit;
use AcMarche\Stock\Form\QuantiteType;
use AcMarche\Stock\Service\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Stock\Controller
 * @Route("/quantite")
 * @IsGranted("ROLE_TRAVAUX_STOCK")
 */
class QuantiteController extends AbstractController
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/{id}", name="stock_quantite_update")
     * IsGranted("ROLE_STOCK")
     */
    public function index(Request $request, Produit $produit)
    {
        $form = $this->createForm(QuantiteType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->log($produit, $form->getData()->getQuantite());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Les quantités ont bien été mise à jour.');

            return $this->redirectToRoute('stock_produit_show', ['id' => $produit->getId()]);
        }

        return $this->render(
            '@AcMarcheStock/quantite/index.html.twig',
            ['produit' => $produit, 'form' => $form->createView()]
        );
    }
}
