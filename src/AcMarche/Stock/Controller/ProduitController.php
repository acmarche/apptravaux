<?php

namespace AcMarche\Stock\Controller;

use AcMarche\Stock\Entity\Produit;
use AcMarche\Stock\Form\ProduitType;
use AcMarche\Stock\Repository\ProduitRepository;
use AcMarche\Stock\Service\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produit")
 * @IsGranted("ROLE_TRAVAUX_STOCK")
 */
class ProduitController extends AbstractController
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
     * @Route("/", name="stock_produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render(
            'stock/produit/index.html.twig',
            [
                'produits' => $produitRepository->getAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="stock_produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit a bien été créé.');

            return $this->redirectToRoute('stock_produit_index');
        }

        return $this->render(
            'stock/produit/new.html.twig',
            [
                'produit' => $produit,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="stock_produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render(
            'stock/produit/show.html.twig',
            [
                'produit' => $produit,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="stock_produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->logger->log($produit, $form->getData()->getQuantite());

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le produit a bien été modifié.');

            return $this->redirectToRoute(
                'stock_produit_index',
                [
                    'id' => $produit->getId(),
                ]
            );
        }

        return $this->render(
            'stock/produit/edit.html.twig',
            [
                'produit' => $produit,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="stock_produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit a bien été supprimé.');
        }

        return $this->redirectToRoute('stock_produit_index');
    }
}
