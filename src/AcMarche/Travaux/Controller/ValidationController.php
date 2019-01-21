<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Event\InterventionEvent;
use AcMarche\Travaux\Form\ValidationType;
use AcMarche\Travaux\Service\InterventionWorkflow;
use AcMarche\Travaux\Service\TravauxUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 *
 * @Route("/validation")
 * @IsGranted("ROLE_TRAVAUX_VALIDATION")
 */
class ValidationController extends AbstractController
{
    /**
     * @var TravauxUtils
     */
    private $travauxUtils;
    /**
     * @var InterventionWorkflow
     */
    private $interventionWorkflow;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        TravauxUtils $travauxUtils,
        EventDispatcherInterface $eventDispatcher,
        InterventionWorkflow $interventionWorkflow,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->travauxUtils = $travauxUtils;
        $this->interventionWorkflow = $interventionWorkflow;
        $this->authorizationChecker = $authorizationChecker;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Lists des interventions en attentes
     *
     * @Route("/", name="validation", methods={"GET"})
     *
     */
    public function index()
    {
        $interventions = $this->travauxUtils->getInterventionsEnAttentes();

        return $this->render('validation/index.html.twig', array('entities' => $interventions));
    }

    /**
     * Formulaire pour valider l'intervention
     *
     * @Route("/{id}", name="validation_show", methods={"GET","POST"})
     *
     */
    public function show(Request $request, Intervention $intervention)
    {
        $form = $this->createValidationForm($intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $message = $form->get('message')->getData();
            $event = new InterventionEvent($intervention, $message);

            if ($form->get('accepter')->isClicked()) {
                $result = $this->interventionWorkflow->applyAccepter($intervention);
                if (isset($result['error'])) {
                    $this->addFlash("danger", $result['error']);
                } else {
                    $this->eventDispatcher->dispatch(InterventionEvent::INTERVENTION_ACCEPT, $event);
                }
            }

            if ($form->get('refuser')->isClicked()) {
                $result = $this->interventionWorkflow->applyRefuser($intervention);
                if (isset($result['error'])) {
                    $this->addFlash("danger", $result['error']);
                } else {
                    $this->eventDispatcher->dispatch(InterventionEvent::INTERVENTION_REJECT, $event);
                }

                //redirect to list because intervention deleted
                return $this->redirectToRoute('intervention');
            }

            if ($form->has('plusinfo')) {
                if ($form->get('plusinfo')->isClicked()) {
                    $result = $this->interventionWorkflow->applyPlusInfo($intervention);
                    if (isset($result['error'])) {
                        $this->addFlash("danger", $result['error']);
                    } else {
                        $this->eventDispatcher->dispatch(InterventionEvent::INTERVENTION_INFO, $event);
                    }
                }
            }

            if (!isset($result['error'])) {
                $em->flush();
            }

            return $this->redirectToRoute('intervention_show', array('id' => $intervention->getId()));
        }

        return $this->render(
            'validation/show.html.twig',
            array(
                'entity' => $intervention,
                'form' => $form->createView(),
                'pdf' => false,
            )
        );
    }

    private function createValidationForm(
        Intervention $intervention
    ) {
        $form = $this->createForm(
            ValidationType::class,
            $intervention,
            array(
                'action' => $this->generateUrl('validation_show', array('id' => $intervention->getId())),
                'method' => 'POST',
            )
        );

        if ($this->authorizationChecker->isGranted("ROLE_TRAVAUX_ADMIN")) {
            $form->add(
                'plusinfo',
                SubmitType::class,
                array(
                    'label' => 'Plus d\'infos',
                    'attr' => array('class' => 'btn-warning'),
                )
            );
        }

        return $form;
    }
}
