<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\Form\CommentaireType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AcMarche\Avaloir\Entity\Commentaire;
use AcMarche\Avaloir\Form\NettoyageQuartierType;
use AcMarche\Avaloir\Entity\Quartier;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Commentaire controller.
 *
 * @Route("/commentaire")
 * @IsGranted("ROLE_TRAVAUX_AVALOIR")
 */
class CommentaireController extends AbstractController
{

    /**
     * Finds and displays a Date nettoyage entity.
     *
     * @Route("/{id}", name="commentaire_show", methods={"GET"})
     *
     */
    public function show(Commentaire $commentaire)
    {
        return $this->render(
            '@AcMarcheAvaloir/commentaire/show.html.twig',
            array(
                'commentaire' => $commentaire,
            )
        );
    }


    /**
     * Displays a form to create a new Commentaire entity.
     *
     * @Route("/new/{id}", name="commentaire_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request, Avaloir $avaloir)
    {
        $commentaire = new Commentaire($avaloir);

        $form = $this->createForm(CommentaireType::class, $commentaire)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            $this->addFlash('success', "Le commentaire a bien été ajouté");

            return $this->redirect($this->generateUrl('avaloir_show', array('id' => $avaloir->getId())));
        }

        return $this->render(
            '@AcMarcheAvaloir/commentaire/new.html.twig',
            array(
                'commentaire' => $commentaire,
                'form' => $form->createView(),
            )
        );
    }

    /**
     *
     *
     * @Route("/{id}/edit", name="commentaire_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(CommentaireType::class, $commentaire)
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash("success", "Le commentaire a bien été modifié");

            return $this->redirectToRoute('avaloir_show', array('id' => $commentaire->getAvaloir()->getId()));
        }

        return $this->render(
            '@AcMarcheAvaloir/commentaire/edit.html.twig',
            array(
                'commentaire' => $commentaire,
                'avaloir' => $commentaire->getAvaloir(),
                'form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Commentaire entity.
     *
     * @Route("/{id}", name="commentaire_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Commentaire $dateNettoyage)
    {
        $avaloir = $dateNettoyage->getAvaloir();

        if ($this->isCsrfTokenValid('delete'.$dateNettoyage->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dateNettoyage);
            $em->flush();

            $this->addFlash('success', "La date a bien été supprimée");
        }

        return $this->redirect($this->generateUrl('avaloir_show', array('id' => $avaloir->getId())));
    }


}
