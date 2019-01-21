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
            'domaine/index.html.twig',
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
            'domaine/new.html.twig',
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
        $deleteForm = $this->createDeleteForm($domaine->getId());

        return $this->render(
            'domaine/show.html.twig',
            array(
                'entity' => $domaine,
                'delete_form' => $deleteForm->createView(),
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
            'domaine/edit.html.twig',
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
    public function delete(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Domaine::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Domaine entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'Le type a bien été supprimé.');
        }

        return $this->redirectToRoute('domaine');
    }

    /**
     * Creates a form to delete a Domaine entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('domaine_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
