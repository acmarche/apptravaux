<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Quartier;
use AcMarche\Avaloir\Entity\Rue;
use AcMarche\Avaloir\Form\QuartierType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render(
            'avaloir/quartier/index.html.twig',
            array(
                'entities' => $entities,
            )
        );
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

        return $this->render(
            'avaloir/quartier/new.html.twig',
            array(
                'entity' => $quartier,
                'form' => $form->createView(),
            )
        );
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

        $rues = $em->getRepository(Rue::class)->getByQuartier($quartier, true);

        return $this->render(
            'avaloir/quartier/show.html.twig',
            array(
                'listrues' => $rues,
                'entity' => $quartier,
            )
        );
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

        return $this->render(
            'avaloir/quartier/edit.html.twig',
            array(
                'entity' => $quartier,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Quartier entity.
     *
     * @Route("/{id}", name="quartier_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Quartier $quartier)
    {
        if ($this->isCsrfTokenValid('delete'.$quartier->getId(), $request->request->get('_token'))) {

            $em = $this->getDoctrine()->getManager();

            $em->remove($quartier);
            $em->flush();

            $this->addFlash("success", "Le quartier a bien été effacé");
        }

        return $this->redirect($this->generateUrl('quartier'));
    }

}
