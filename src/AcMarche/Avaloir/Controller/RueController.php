<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Rue;
use AcMarche\Avaloir\Form\RueType;
use AcMarche\Avaloir\Form\Search\SearchRueType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Rue controller.
 *
 * @Route("/rue")
 * @IsGranted("ROLE_TRAVAUX_AVALOIR")
 *
 */
class RueController extends AbstractController
{

    /**
     * Lists all Rue entities.
     *
     * @Route("/", name="rue", methods={"GET"})
     *
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $data = array();

        if ($session->has("rue_search")) {
            $data = unserialize($session->get("rue_search"));
        }

        $search_form = $this->createForm(
            SearchRueType::class,
            $data,
            array(
                'action' => $this->generateUrl('rue'),
                'method' => 'GET',
            )
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();

            if ($search_form->get('raz')->isClicked()) {
                $session->remove("rue_search");
                $this->addFlash('info', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('rue');
            }
        }

        $session->set('rue_search', serialize($data));
        $entities = $em->getRepository(Rue::class)->search($data);

        return $this->render(
            '@AcMarcheAvaloir/rue/index.html.twig',
            array(
                'search_form' => $search_form->createView(),
                'entities' => $entities,
            )
        );
    }

    /**
     * Displays a form to create a new Rue entity.
     *
     * @Route("/new", name="rue_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request)
    {
        $rue = new Rue();

        $form = $this->createForm(RueType::class, $rue)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rue);
            $em->flush();

            $this->addFlash("success", "La rue a bien été ajoutée");

            return $this->redirectToRoute('rue_show', array('id' => $rue->getId()));
        }

        return $this->render(
            '@AcMarcheAvaloir/rue/new.html.twig',
            array(
                'entity' => $rue,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Rue entity.
     *
     * @Route("/{id}", name="rue_show", methods={"GET"})
     *
     */
    public function show(Rue $rue)
    {
        return $this->render(
            '@AcMarcheAvaloir/rue/show.html.twig',
            array(
                'entity' => $rue,
            )
        );
    }

    /**
     * Displays a form to edit an existing Rue entity.
     *
     * @Route("/{id}/edit", name="rue_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Rue $rue)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(RueType::class, $rue)
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash("success", "La rue a bien été modifiée");

            return $this->redirectToRoute('rue');
        }

        return $this->render(
            '@AcMarcheAvaloir/rue/edit.html.twig',
            array(
                'entity' => $rue,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Rue entity.
     *
     * @Route("/{id}", name="rue_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Rue $rue)
    {
        if ($this->isCsrfTokenValid('delete'.$rue->getId(), $request->request->get('_token'))) {

            $em = $this->getDoctrine()->getManager();

            $avaloirs = $rue->getAvaloirs();
            foreach ($avaloirs as $avaloir) {
                $em->remove($avaloir);
            }

            $em->remove($rue);
            $em->flush();
            $this->addFlash("success", "La rue a bien été supprimée");
        }

        return $this->redirectToRoute('rue');
    }

    /**
     * Pour remplir l'auto completion
     *
     * @Route("/rueautocomplete/{query}", name="rue_autocomplete", methods={"GET"})
     *
     */
    public function rueAutocomplete($query = null)
    {
        $response = new JsonResponse();

        if (!$query) {
            $response->setData(array("results" => array()));

            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $rues = $em->getRepository(Rue::class)->search(array('nom' => $query));

        $data = array();
        $i = 0;
        foreach ($rues as $rue) {
            $village = $rue->getVillage();
            $data[$i]["label"] = $rue->getNom();
            $data[$i]["value"] = $rue->getId();
            $data[$i]["village"] = $village->getNom();
            $i++;
        }

        $response->setData($data);

        return $response;
    }
}
