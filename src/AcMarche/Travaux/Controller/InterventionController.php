<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Entity\Suivi;
use AcMarche\Travaux\Event\InterventionEvent;
use AcMarche\Travaux\Form\InterventionType;
use AcMarche\Travaux\Form\Search\SearchInterventionType;
use AcMarche\Travaux\Service\FileHelper;
use AcMarche\Travaux\Service\InterventionWorkflow;
use AcMarche\Travaux\Service\TravauxUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Intervention controller.
 *
 * @Route("/intervention")
 * @IsGranted("ROLE_TRAVAUX")
 */
class InterventionController extends AbstractController
{
    /**
     * @var TravauxUtils
     */
    private $travauxUtils;
    /**
     * @var FileHelper
     */
    private $fileHelper;
    /**
     * @var InterventionWorkflow
     */
    private $workflow;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        TravauxUtils $travauxUtils,
        FileHelper $fileHelper,
        InterventionWorkflow $workflow,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session
    ) {
        $this->travauxUtils = $travauxUtils;
        $this->fileHelper = $fileHelper;
        $this->workflow = $workflow;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
    }

    /**
     * Lists all Intervention entities.
     *
     * @Route("/", name="intervention")
     * @Route("/ancre/{anchor}/", name="intervention_anchor", methods={"GET"})
     *
     */
    public function index(Request $request, $anchor = null)
    {
        $em = $this->getDoctrine()->getManager();

        $key = "intervention_search";
        $data = [];

        if ($this->session->has($key)) {
            $data = unserialize($this->session->get($key));
        }

        if ($categorieIntervention = $this->travauxUtils->getCategorieDefault('intervention')) {
            $data['categorie'] = $categorieIntervention->getId(); //intervention
        }

        $user = $this->getUser();
        $data['user'] = $user;

        $data = array_merge($data, $this->travauxUtils->getConstraintsForUser());

        $search_form = $this->createForm(
            SearchInterventionType::class,
            $data,
            array(
                'method' => 'GET',
            )
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();

            if ($search_form->get('raz')->isClicked()) {
                $this->session->remove($key);
                $this->addFlash('info', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('intervention');
            }
        }

        $this->session->set($key, serialize($data));
        $interventions = $em->getRepository(Intervention::class)->search($data);

        $this->travauxUtils->setLastSuivisForInterventions($interventions);

        return $this->render(
            'travaux/intervention/index.html.twig',
            array(
                'search_form' => $search_form->createView(),
                'entities' => $interventions,
                'anchor' => $anchor,
            )
        );
    }

    /**
     * Displays a form to create a new Intervention entity.
     *
     * @Route("/new", name="intervention_new", methods={"GET","POST"})
     * @IsGranted("ROLE_TRAVAUX_ADD")
     *
     */
    public function new(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $intervention = new Intervention();

        $form = $this->createForm(
            InterventionType::class,
            $intervention
        )
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $intervention->setUserAdd($user->getUsername());

            $em->persist($intervention);
            $em->flush();

            $this->workflow->newIntervention($intervention);

            $em->flush();
            $this->addFlash('success', 'L\'intervention a bien été crée.');

            $event = new InterventionEvent($intervention, null);
            $this->eventDispatcher->dispatch(InterventionEvent::INTERVENTION_NEW, $event);

            return $this->redirectToRoute('intervention_show', array('id' => $intervention->getId()));
        }

        return $this->render(
            'travaux/intervention/new.html.twig',
            array(
                'entity' => $intervention,
                'form' => $form->createView(),
            )
        );
    }


    /**
     * Finds and displays a Intervention entity.
     *
     * @Route("/{id}", name="intervention_show", methods={"GET"})
     *
     */
    public function show(Intervention $intervention)
    {
        $em = $this->getDoctrine()->getManager();

        $deleteFormSuivis = $this->createSuivisDeleteForm($intervention->getId());

        $suivis = $em->getRepository(Suivi::class)->search(
            array('intervention' => $intervention)
        );

        return $this->render(
            'travaux/intervention/show.html.twig',
            array(
                'intervention' => $intervention,
                'suivis' => $suivis,
                'delete_form_suivis' => $deleteFormSuivis->createView(),
                'pdf' => false,
            )
        );
    }

    /**
     * Displays a form to edit an existing Intervention entity.
     *
     * @Route("/{id}/edit", name="intervention_edit", methods={"GET","POST"})
     *
     * @IsGranted("edit", subject="intervention")
     *
     */
    public function edit(Request $request, Intervention $intervention)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(
            InterventionType::class,
            $intervention
        )
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $this->addFlash('success', 'L\'intervention a bien été modifiée.');

            return $this->redirectToRoute('intervention_show', array('id' => $intervention->getId()));
        }

        return $this->render(
            'travaux/intervention/edit.html.twig',
            array(
                'entity' => $intervention,
                'form' => $editForm->createView(),
            )
        );
    }

    /**
     * @Route("/{id}", name="intervention_delete", methods={"DELETE"})
     * @IsGranted("delete", subject="intervention")
     */
    public function delete(Request $request, Intervention $intervention): Response
    {
        if ($this->isCsrfTokenValid('delete'.$intervention->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            try {
                $this->fileHelper->deleteAllDocs($intervention);
                $em->remove($intervention);

                $em->flush();
                $this->addFlash('success', 'L\'intervention a bien été supprimée.');
            } catch (IOException $exception) {
                $this->addFlash("danger", "Erreur de la suppression des pièce jointes: ".$exception->getMessage());
            }
        }

        return $this->redirectToRoute('intervention');
    }

    /**
     * Deletes a Suivis entity.
     *
     * @Route("/suivis/delete/{id}", name="suivis_delete", methods={"DELETE"})
     *
     */
    public function deleteSuivis(Request $request, Intervention $intervention)
    {
        $form = $this->createSuivisDeleteForm($intervention->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $suivis = $request->get('suivis', array());

            if (count($suivis) < 1) {
                $this->addFlash('warning', "Aucun suivis sélectionné");

                return $this->redirectToRoute('intervention_show', array('id' => $intervention->getid()));
            }

            $user = $this->getUser();

            foreach ($suivis as $suivis_id) {
                $suivi = $em->getRepository(Suivi::class)->find($suivis_id);

                if ($suivi) {
                    $userAdd = $suivi->getUserAdd();
                    if ($userAdd == $user->getUsername()) {
                        $em->remove($suivi);
                    } else {
                        $this->addFlash('danger', "Seul celui qui a ajouté le suivi peut le supprimer");
                    }
                }
            }

            $em->flush();
            $this->addFlash('success', 'Le(s) suivi(s) ont bien été supprimé(s)');

            return $this->redirectToRoute('intervention_show', array('id' => $intervention->getId()));
        }

        return $this->redirectToRoute('intervention');
    }

    /**
     * Creates a form to delete a Suivis entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createSuivisDeleteForm($intervention_id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('suivis_delete', array('id' => $intervention_id)))
            ->setMethod('DELETE')
            ->getForm();
    }

}
