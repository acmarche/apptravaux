<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Avaloir;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AcMarche\Avaloir\Entity\DateNettoyage;
use AcMarche\Avaloir\Form\DateNettoyageType;
use AcMarche\Avaloir\Form\NettoyageQuartierType;
use AcMarche\Avaloir\Entity\Quartier;
use Symfony\Component\Routing\Annotation\Route;

/**
 * DateNettoyage controller.
 *
 * @Route("/datenettoyage")
 * @IsGranted("ROLE_TRAVAUX_AVALOIR")
 */
class DateNettoyageController extends AbstractController
{

    /**
     * Finds and displays a Date nettoyage entity.
     *
     * @Route("/{id}", name="datenettoyage_show", methods={"GET"})
     *
     */
    public function show(DateNettoyage $date)
    {
        $deleteForm = $this->createDeleteForm($date->getId());

        return $this->render('date_nettoyage/show.html.twig', array(
            'entity' => $date,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a DateNettoyage entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('datenettoyage_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }

    /**
     * Displays a form to create a new DateNettoyage entity.
     *
     * @Route("/new/{id}", name="datenettoyage_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request, Avaloir $avaloir)
    {
        $dateNettoyage = new DateNettoyage();
        $dateNettoyage->setAvaloir($avaloir);

        $form = $this->createForm(
            DateNettoyageType::class,
            $dateNettoyage,
            [
                'action' => $this->generateUrl(
                    'datenettoyage_new',
                    ['id' => $avaloir->getId()]
                ),
            ]
        )
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dateNettoyage);
            $em->flush();

            $this->addFlash('success', "La date a bien été ajoutée");

            return $this->redirect($this->generateUrl('avaloir_show', array('id' => $avaloir->getId())));
        }

        return $this->render('date_nettoyage/new.html.twig', array(
            'entity' => $dateNettoyage,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a DateNettoyage entity.
     *
     * @Route("/{id}", name="datenettoyage_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DateNettoyage $dateNettoyage)
    {
        $form = $this->createDeleteForm($dateNettoyage->getId());
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        $avaloir = $dateNettoyage->getAvaloir();

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($dateNettoyage);
            $em->flush();

            $this->addFlash('success', "La date a bien été supprimée");
        }

        return $this->redirect($this->generateUrl('avaloir_show', array('id' => $avaloir->getId())));
    }

    /**
     * Displays a form to create a new Suivis entity.
     *
     * @Route("/new/quartier/{slugname}", name="nettoyage_quartier_new", methods={"GET","POST"})
     *
     */
    public function nettoyageQuartierNew(Request $request, Quartier $quartier)
    {
        $entity = new DateNettoyage();
        $entity->setQuartier($quartier);

        $form = $this->createQuartierCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $rues = $quartier->getRues();
            $checkAvaloirs = false;

            foreach ($rues as $rue) {
                $avaloirs = $rue->getAvaloirs();
                foreach ($avaloirs as $avaloir) {
                    $checkAvaloirs = true;
                    $dateClone = clone($entity);
                    $dateClone->setAvaloir($avaloir);
                    $em->persist($dateClone);
                }
            }

            if (!$checkAvaloirs) {
                $this->addFlash("error", "Aucun avaloir associé à ce quartier !");
            } else {
                $em->flush();

                $this->addFlash('success', "La date a bien été ajoutée");
            }

            return $this->redirect($this->generateUrl('quartier_show', array('slugname' => $quartier->getSlugname())));
        }

        return $this->render('date_nettoyage/nettoyage_quartier_new.html.twig', array(
            'entity' => $entity,
            'quartier' => $quartier,
            'form' => $form->createView(),
        ));
    }

    private function createQuartierCreateForm(DateNettoyage $entity)
    {
        $form = $this->createForm(
            NettoyageQuartierType::class,
            $entity,
            array(
                'action' => $this->generateUrl(
                    'nettoyage_quartier_new',
                    array(
                        'slugname' => $entity->getQuartier()->getSlugname(),
                    )
                ),
                'method' => 'POST',
            )
        );

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }
}
