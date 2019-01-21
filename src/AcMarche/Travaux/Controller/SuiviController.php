<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Entity\Suivi;
use AcMarche\Travaux\Event\InterventionEvent;
use AcMarche\Travaux\Form\SuiviType;
use AcMarche\Travaux\Service\SuiviService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Suivi controller.
 *
 * @Route("/suivi")
 * @IsGranted("ROLE_TRAVAUX")
 */
class SuiviController extends AbstractController
{
    /**
     * @var SuiviService
     */
    private $suiviService;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        SuiviService $suiviService,
        EventDispatcherInterface $dispatcher
    ) {
        $this->suiviService = $suiviService;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Displays a form to create a new Suivi entity.
     *
     * @Route("/new/{id}", name="suivi_new", methods={"GET","POST"})
     *
     */
    public function new(
        Request $request,
        Intervention $intervention
    ) {
        $suivi = $this->suiviService->initSuivi($intervention);

        $form = $this->createForm(
            SuiviType::class,
            $suivi,
            [
                'action' => $this->generateUrl('suivi_new', array('id' => $intervention->getId())),
            ]
        )
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->suiviService->newSuivi($intervention, $form->getData()->getDescriptif());

            $event = new InterventionEvent($intervention, null, $suivi);
            $this->dispatcher->dispatch(InterventionEvent::INTERVENTION_SUIVI_NEW, $event);

            $intervention = $suivi->getIntervention();

            $this->addFlash('success', 'Le suivi a bien été créé.');

            return $this->redirectToRoute('intervention_show', array('id' => $intervention->getId()));
        }

        return $this->render('suivi/new.html.twig',  array(
            'entity' => $suivi,
            'intervention' => $intervention,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Suivi entity.
     *
     * @Route("/{id}/edit", name="suivi_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Suivi $suivi)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(SuiviType::class, $suivi)
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $user = $this->getUser();
            $intervention = $suivi->getIntervention();
            $userAdd = $suivi->getUserAdd();

            if ($userAdd == $user->getUsername()) {
                $intervention->setUpdated(new \DateTime());
                $em->persist($intervention);
                $em->flush();
                $this->addFlash('success', 'Le suivi a bien été mis à jour.');
            } else {
                $this->addFlash('warning', "Seul celui qui a ajouté le suivi peut le modifier");
            }

            return $this->redirectToRoute('intervention_show', array('id' => $intervention->getId()));
        }

        return $this->render('suivi/edit.html.twig',  array(
            'entity' => $suivi,
            'edit_form' => $editForm->createView(),
        ));
    }
}
