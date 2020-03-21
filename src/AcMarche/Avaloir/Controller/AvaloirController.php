<?php


namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\Entity\DateNettoyage;
use AcMarche\Avaloir\Entity\Rue;
use AcMarche\Avaloir\Form\AvaloirEditType;
use AcMarche\Avaloir\Form\AvaloirType;
use AcMarche\Avaloir\Form\Search\SearchAvaloirType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Avaloir controller.
 *
 * @IsGranted("ROLE_TRAVAUX_AVALOIR")
 *
 * @Route("/avaloir")
 */
class AvaloirController extends AbstractController
{
    /**
     * Lists all Avaloir entities.
     *
     * @Route("/", name="avaloir", methods={"GET"})
     *
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $key = 'avaloir_search';

        $data = array();

        if ($session->has($key)) {
            $data = unserialize($session->get($key));
        }

        $search_form = $this->createForm(
            SearchAvaloirType::class,
            $data,
            array(
                'action' => $this->generateUrl('avaloir'),
                'method' => 'GET',
            )
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();

            if ($search_form->get('raz')->isClicked()) {
                $session->remove($key);
                $this->addFlash('info', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('avaloir');
            }
        }

        $session->set($key, serialize($data));
        $avaloirs = $em->getRepository(Avaloir::class)->search($data);

        return $this->render(
            '@AcMarcheAvaloir/avaloir/index.html.twig',
            array(
                'search_form' => $search_form->createView(),
                'avaloirs' => $avaloirs,
            )
        );
    }

    /**
     * Displays a form to create a new Avaloir entity.
     *
     * Route("/new", name="avaloir_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request)
    {
        $avaloir = new Avaloir();
        $jour = new DateNettoyage();
        $avaloir->addDate($jour);

        $form = $this->createForm(AvaloirType::class, $avaloir)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $data = $form->getData();
            $rueId = $data->getRueId();
            $dates = $data->getDates();

            if ($dates[0] instanceof DateNettoyage) {
                $jour2 = $dates[0]->getJour();
                if ($jour2) {
                    $jour->setAvaloir($avaloir);
                } else {
                    $avaloir->removeDate($jour);
                }
            }

            $rue = false;
            if ($rueId) {
                $rue = $em->getRepository(Rue::class)->find($rueId);
            }

            if (!$rue) {
                $this->addFlash("error", "La rue que vous avez choisi ne se trouve pas dans la liste des rues");

                return $this->redirectToRoute('avaloir_new');
            }

            $avaloir->setRue($rue);
            $em->persist($avaloir);
            $em->flush();
            $this->addFlash("success", "L'avaloir a bien été créé");

            return $this->redirectToRoute('avaloir');
        }

        return $this->render(
            '@AcMarcheAvaloir/avaloir/new.html.twig',
            array(
                'entity' => $avaloir,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Avaloir entity.
     *
     * @Route("/{id}", name="avaloir_show", methods={"GET"})
     *
     */
    public function show(Avaloir $avaloir)
    {
        return $this->render(
            '@AcMarcheAvaloir/avaloir/show.html.twig',
            array(
                'avaloir' => $avaloir,
            )
        );
    }

    /**
     * Displays a form to edit an existing Avaloir entity.
     *
     * @Route("/{id}/edit", name="avaloir_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Avaloir $avaloir)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(AvaloirEditType::class, $avaloir)
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash("success", "L'avaloir a bien été modifié");

            return $this->redirectToRoute('avaloir_show', array('id' => $avaloir->getId()));
        }

        return $this->render(
            '@AcMarcheAvaloir/avaloir/edit.html.twig',
            array(
                'entity' => $avaloir,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Avaloir entity.
     *
     * @Route("/{id}", name="avaloir_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Avaloir $avaloir)
    {
        if ($this->isCsrfTokenValid('delete'.$avaloir->getId(), $request->request->get('_token'))) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($avaloir);
            $em->flush();
            $this->addFlash("success", "L'avaloir a bien été supprimé");
        }

        return $this->redirectToRoute('avaloir');
    }

}
