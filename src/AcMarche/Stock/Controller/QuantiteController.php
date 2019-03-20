<?php


namespace AcMarche\Stock\Controller;

use AcMarche\Stock\Entity\Produit;
use AcMarche\Stock\Form\QuantiteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Stock\Controller
 * @Route("/quantite")
 */
class QuantiteController extends AbstractController
{
    /**
     * @Route("/{id}", name="stock_quantite_update")
     * IsGranted("ROLE_STOCK")
     */
    public function index(Request $request, Produit $produit)
    {
        $form = $this->createForm(QuantiteType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Les quantités ont bien été mise à jour.');

            return $this->redirectToRoute('stock_produit_show', ['id' => $produit->getId()]);
        }

        return $this->render('stock/quantite/index.html.twig', ['produit' => $produit, 'form' => $form->createView()]);
    }
}
