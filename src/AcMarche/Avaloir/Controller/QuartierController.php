<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Rue;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AcMarche\Avaloir\Entity\Quartier;
use AcMarche\Avaloir\Form\QuartierType;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Quartier controller.
 *
 * @Route("/quartier")
 * @IsGranted("ROLE_TRAVAUX_AVALOIR")
 *
 */
class QuartierController extends AbstractController
{

    /**
     * Lists all Quartier entities.
     *
     * @Route("/", name="quartier", methods={"GET"})
     *
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Quartier::class)->search(array());

        return $this->render('quartier/index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Displays a form to create a new Quartier entity.
     *
     * @Route("/new", name="quartier_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request)
    {
        $quartier = new Quartier();

        $form = $this->createForm(QuartierType::class, $quartier)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($quartier);
            $em->flush();

            $this->addFlash("success", "Le quartier a bien été créé");

            return $this->redirect($this->generateUrl('quartier'));
        }
        return $this->render('quartier/new.html.twig', array(
            'entity' => $quartier,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Quartier entity.
     *
     * @Route("/{slugname}", name="quartier_show", methods={"GET"})
     *
     */
    public function show(Quartier $quartier)
    {
        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createDeleteForm($quartier->getId());
        $rues = $em->getRepository(Rue::class)->getByQuartier($quartier, true);

        return $this->render('quartier/show.html.twig', array(
            'listrues' => $rues,
            'entity' => $quartier,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Quartier entity.
     *
     * @Route("/{slugname}/edit", name="quartier_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Quartier $quartier)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(QuartierType::class, $quartier)
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash("success", "Le quartier a bien été modifié");

            return $this->redirect($this->generateUrl('quartier'));
        }
        return $this->render('quartier/edit.html.twig', array(
            'entity' => $quartier,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a Quartier entity.
     *
     * @Route("/{id}", name="quartier_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Quartier $quartier)
    {
        $form = $this->createDeleteForm($quartier->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($quartier);
            $em->flush();

            $this->addFlash("success", "Le quartier a bien été effacé");
        }

        return $this->redirect($this->generateUrl('quartier'));
    }

    /**
     * Creates a form to delete a Quartier entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('quartier_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
