<?php

namespace AcMarche\Travaux\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use AcMarche\Travaux\Entity\Batiment;
use AcMarche\Travaux\Form\BatimentType;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Batiment controller.
 *
 * @Route("/batiment")
 * @IsGranted("ROLE_TRAVAUX_ADMIN")
 */
class BatimentController extends AbstractController
{
    /**
     * Lists all Batiment entities.
     *
     * @Route("/", name="batiment", methods={"GET"})
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Batiment::class)->findAll();

        return $this->render('travaux/batiment/index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Displays a form to create a new Batiment entity.
     *
     * @Route("/new", name="batiment_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request)
    {
        $batiment = new Batiment();

        $form = $this->createForm(BatimentType::class, $batiment)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($batiment);
            $em->flush();

            $this->addFlash('success', 'Le bâtiment a bien été créé.');

            return $this->redirectToRoute('batiment_show', array('slugname' => $batiment->getSlugname()));
        }

        return $this->render('travaux/batiment/new.html.twig', array(
            'entity' => $batiment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Batiment entity.
     *
     * @Route("/{slugname}", name="batiment_show", methods={"GET"})
     *
     */
    public function show(Batiment $batiment)
    {
        $deleteForm = $this->createDeleteForm($batiment->getId());

        return $this->render('travaux/batiment/show.html.twig', array(
            'entity' => $batiment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Batiment entity.
     *
     * @Route("/{slugname}/edit", name="batiment_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Batiment $batiment)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(BatimentType::class, $batiment)
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le bâtiment a bien été modifié.');
            return $this->redirectToRoute('batiment_show', array('slugname' => $batiment->getSlugname()));
        }

        return $this->render('travaux/batiment/edit.html.twig', array(
            'entity' => $batiment,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Batiment entity.
     *
     * @Route("/{id}", name="batiment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Batiment::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Batiment entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'Le bâtiment a bien été supprimé.');
        }

        return $this->redirectToRoute('batiment');
    }

    /**
     * Creates a form to delete a Batiment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('batiment_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array(
                'label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
