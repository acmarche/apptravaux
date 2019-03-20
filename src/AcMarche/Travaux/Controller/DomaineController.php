<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Entity\Domaine;
use AcMarche\Travaux\Form\DomaineType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Domaine controller.
 *
 * @Route("/domaine")
 * @IsGranted("ROLE_TRAVAUX_ADMIN")
 */
class DomaineController extends AbstractController
{

    /**
     * Lists all Domaine entities.
     *
     * @Route("/", name="domaine", methods={"GET"})
     *
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Domaine::class)->findAll();

        return $this->render(
            'travaux/domaine/index.html.twig',
            array(
                'entities' => $entities,
            )
        );
    }

    /**
     * Displays a form to create a new Domaine entity.
     *
     * @Route("/new", name="domaine_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request)
    {
        $domaine = new Domaine();

        $form = $this->createForm(DomaineType::class, $domaine)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($domaine);
            $em->flush();

            $this->addFlash('success', 'Le type a bien été créé.');

            return $this->redirectToRoute('domaine_show', array('slugname' => $domaine->getSlugname()));
        }

        return $this->render(
            'travaux/domaine/new.html.twig',
            array(
                'entity' => $domaine,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Domaine entity.
     *
     * @Route("/{slugname}", name="domaine_show", methods={"GET"})
     *
     */
    public function show(Domaine $domaine)
    {
        return $this->render(
            'travaux/domaine/show.html.twig',
            array(
                'entity' => $domaine,
            )
        );
    }

    /**
     * Displays a form to edit an existing Domaine entity.
     *
     * @Route("/{slugname}/edit", name="domaine_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Domaine $domaine)
    {
        $editForm = $this->createForm(DomaineType::class, $domaine)
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Le type a bien été mis à jour.');

            return $this->redirectToRoute('domaine_show', array('slugname' => $domaine->getSlugname()));
        }

        return $this->render(
            'travaux/domaine/edit.html.twig',
            array(
                'entity' => $domaine,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Domaine entity.
     *
     * @Route("/{id}", name="domaine_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Domaine $domaine)
    {
        if ($this->isCsrfTokenValid('delete'.$domaine->getId(), $request->request->get('_token'))) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($domaine);
            $em->flush();

            $this->addFlash('success', 'Le type a bien été supprimé.');
        }

        return $this->redirectToRoute('domaine');
    }

}
