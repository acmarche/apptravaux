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
            'categorie/index.html.twig',
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
            'categorie/new.html.twig',
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
        $deleteForm = $this->createDeleteForm($categorie->getId());

        return $this->render(
            'categorie/show.html.twig',
            array(
                'entity' => $categorie,
                'delete_form' => $deleteForm->createView(),
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
            'categorie/edit.html.twig',
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
    public function delete(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Categorie::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Categorie entity.');
            }

            $intervention = $entity->getIntervention();

            if (count($intervention) > 0) {
                $this->addFlash(
                    'warning',
                    "Cette catégorie ne peut être supprimée car des intervention sont classés dans celle-ci"
                );

                return $this->redirectToRoute('categorie');
            }

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'La catégorie a bien été supprimée.');
        }

        return $this->redirectToRoute('categorie');
    }

    /**
     * Creates a form to delete a Categorie entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('categorie_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Delete',
                    'attr' => array('class' => 'btn-danger'),
                )
            )
            ->getForm();
    }
}
