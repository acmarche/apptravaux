<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Event\InterventionEvent;
use AcMarche\Travaux\Form\Search\SearchInterventionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Intervention controller.
 *
 * @Route("/archive")
 * @IsGranted("ROLE_TRAVAUX")
 */
class ArchiveController extends AbstractController
{
    /**
     * Liste des interventions archivées.
     *
     * @Route("/", name="intervention_archive", methods={"GET"})
     *
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $data = array();
        $key = "intervention_archive_search";

        if ($session->has($key)) {
            $data = unserialize($session->get($key));
        }

        $user = $this->getUser();
        $data['user'] = $user;
        $data['archive'] = 1; //force archive
        $data['sort'] = 'createdAt';

        /**
         * dans le repository je perds les roles ??
         * auteur doit voir demande des contributeurs et les siennes
         */
        if ($user->hasRole('ROLE_TRAVAUX_AUTEUR')) {
            $data['role'] = 'AUTEUR';
            $data['withAValider'] = true;
        }

        /**
         * contributeur doit voir ses demandes
         * les non valider aussi sinon ne voit pas ce qu'il a encode !
         * absence du cadre a notifier contrairement à l'admin et à l'auteur
         */
        if ($user->hasRole('ROLE_TRAVAUX_CONTRIBUTEUR')) {
            $data['role'] = 'CONTRIBUTEUR';
            $data['withAValider'] = true;
        }

        /**
         * Doit voir ceux non valider sinon ne voit pas ce qu'il a encode
         */
        if ($user->hasRole('ROLE_TRAVAUX_REDACTEUR')) {
            $data['role'] = 'REDACTEUR';
            $data['withAValider'] = true;
        }

        $search_form = $this->createForm(
            SearchInterventionType::class,
            $data,
            array(
                'action' => $this->generateUrl('intervention_archive'),
                'method' => 'GET',
            )
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();

            if ($search_form->get('raz')->isClicked()) {
                $session->remove($key);
                $this->addFlash('info', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('intervention_archive');
            }
        }

        $session->set($key, serialize($data));
        $entities = $em->getRepository(Intervention::class)->search($data);

        return $this->render(
            '@AcMarcheTravaux/travaux/archive/index.html.twig',
            array(
                'search_form' => $search_form->createView(),
                'entities' => $entities,
            )
        );
    }

    /**
     * Archivage d'une intervention
     * @Route("/archiveset/{id}", name="intervention_archive_set", methods={"POST"})
     *
     */
    public function archiveSet(Request $request, Intervention $intervention, EventDispatcherInterface $dispatcher)
    {
        if (!$this->getUser()->hasRole('ROLE_TRAVAUX_REDACTEUR') && !$this->getUser()->hasRole("ROLE_TRAVAUX_ADMIN")) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit d\'archiver');
        }

        if ($this->isCsrfTokenValid('archive'.$intervention->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();

            $event = new InterventionEvent($intervention, null);

            $label = $intervention->getArchive() ? 'désarchivée' : 'archivée';

            if ($intervention->getArchive()) {
                $intervention->setArchive(false);
            } else {
                $intervention->setArchive(true);
                $intervention->setCurrentPlace('published');//force
                $dispatcher->dispatch(InterventionEvent::INTERVENTION_ARCHIVE, $event);
            }

            $em->persist($intervention);
            $em->flush();

            $this->addFlash('success', "L'intervention a bien été $label");
        }

        return $this->redirectToRoute('intervention');
    }

}
