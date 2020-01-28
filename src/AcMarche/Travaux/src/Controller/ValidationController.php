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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


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
    /**
     * @var ValidatorInterface
     */
    private $validator;

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

        return $this->render('@AcMarcheTravaux/travaux/validation/index.html.twig', array('entities' => $interventions));
    }

    /**
     * Formulaire pour valider l'intervention
     *
     * @Route("/{id}", name="validation_show", methods={"GET","POST"})
     *
     */
    public function show(Request $request, Intervention $intervention)
    {
        $form = $this->createForm(ValidationType::class, $intervention);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $data = $form->getData();
            $dateExecution = null;

            $message = $form->get('message')->getData();

            if ($form->has('date_execution') && $data->getDateExecution()) {
                $dateExecution = $form->get('date_execution')->getData();
            }

            $event = new InterventionEvent($intervention, $message, null, $dateExecution);

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

            if ($form->has('reporter')) {
                if ($form->get('reporter')->isClicked()) {

                    if (!$dateExecution) {
                        $this->addFlash('danger', 'Veuillez indiquer une date d\'exécution');

                        return $this->redirectToRoute('validation_show', ['id' => $intervention->getId()]);
                    }

                    $result = $this->interventionWorkflow->applyAccepter($intervention);
                    if (isset($result['error'])) {
                        $this->addFlash("danger", $result['error']);
                    } else {
                        $this->eventDispatcher->dispatch(InterventionEvent::INTERVENTION_REPORTER, $event);
                    }
                }
            }

            if (!isset($result['error'])) {
                $em->flush();
            }

            return $this->redirectToRoute('intervention_show', array('id' => $intervention->getId()));
        }

        return $this->render(
            '@AcMarcheTravaux/travaux/validation/show.html.twig',
            array(
                'entity' => $intervention,
                'form' => $form->createView(),
                'pdf' => false,
            )
        );
    }

}
