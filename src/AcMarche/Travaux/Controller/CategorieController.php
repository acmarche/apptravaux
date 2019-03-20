<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Entity\Categorie;
use AcMarche\Travaux\Form\CategorieType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Categorie controller.
 *
 * @Route("/categorie")
 * @IsGranted("ROLE_TRAVAUX_ADMIN")
 */
class CategorieController extends AbstractController
{

    /**
     * Lists all Categorie entities.
     *
     * @Route("/", name="categorie", methods={"GET"})
     *
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Categorie::class)->findAll();

        return $this->render(
            'travaux/categorie/index.html.twig',
            array(
                'entities' => $entities,
            )
        );
    }

    /**
     * Displays a form to create a new Categorie entity.
     *
     * @Route("/new", name="categorie_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request)
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'La catégorie a bien été crée.');

            return $this->redirectToRoute('categorie_show', array('slugname' => $categorie->getSlugname()));
        }

        return $this->render(
            'travaux/categorie/new.html.twig',
            array(
                'entity' => $categorie,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Categorie entity.
     *
     * @Route("/{slugname}", name="categorie_show", methods={"GET"})
     *
     */
    public function show(Categorie $categorie)
    {
        return $this->render(
            'travaux/categorie/show.html.twig',
            array(
                'entity' => $categorie,
            )
        );
    }

    /**
     * Displays a form to edit an existing Categorie entity.
     *
     * @Route("/{slugname}/edit", name="categorie_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Categorie $categorie)
    {
        $editForm = $this->createForm(CategorieType::class, $categorie)
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été mise à jour.');

            return $this->redirectToRoute('categorie_show', array('slugname' => $categorie->getSlugname()));
        }

        return $this->render(
            'travaux/categorie/edit.html.twig',
            array(
                'entity' => $categorie,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Categorie entity.
     *
     * @Route("/{id}", name="categorie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Categorie $categorie)
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {

            $em = $this->getDoctrine()->getManager();

            $intervention = $categorie->getIntervention();

            if (count($intervention) > 0) {
                $this->addFlash(
                    'warning',
                    "Cette catégorie ne peut être supprimée car des intervention sont classés dans celle-ci"
                );

                return $this->redirectToRoute('categorie');
            }

            $em->remove($categorie);
            $em->flush();

            $this->addFlash('success', 'La catégorie a bien été supprimée.');
        }

        return $this->redirectToRoute('categorie');
    }


}
