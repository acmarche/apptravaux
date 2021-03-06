<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Data\Localite;
use AcMarche\Avaloir\Entity\Rue;
use AcMarche\Avaloir\Form\RueType;
use AcMarche\Avaloir\Form\Search\SearchRueType;
use AcMarche\Avaloir\Repository\RueRepository;
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
     * @var RueRepository
     */
    private $rueRepository;

    public function __construct(RueRepository $rueRepository)
    {
        $this->rueRepository = $rueRepository;
    }

    /**
     * Lists all Rue entities.
     *
     * @Route("/", name="rue", methods={"GET"})
     *
     */
    public function index(Request $request)
    {
        $rues = $this->rueRepository->findAll();

        return $this->render(
            '@AcMarcheAvaloir/rue/index.html.twig',
            array(
                'rues' => $rues,
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
        if ($this->isCsrfTokenValid('delete' . $rue->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();

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
