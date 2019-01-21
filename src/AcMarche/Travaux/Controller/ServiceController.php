<?php

namespace AcMarche\Travaux\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use AcMarche\Travaux\Entity\Service;
use AcMarche\Travaux\Form\ServiceType;

/**
 * Service controller.
 *
 * @Route("/service")
 * @IsGranted("ROLE_TRAVAUX_ADMIN")
 */
class ServiceController extends AbstractController
{

    /**
     * Lists all Service entities.
     *
     * @Route("/", name="service", methods={"GET"})
     *
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Service::class)->findAll();

        return $this->render('service/index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Displays a form to create a new Service entity.
     *
     * @Route("/new", name="service_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request)
    {
        $service = new Service();

        $form = $this->createForm(ServiceType::class, $service)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($service);
            $em->flush();

            $this->addFlash('success', 'Le service a bien été créé.');

            return $this->redirectToRoute('service_show', array('slugname' => $service->getSlugname()));
        }

        return $this->render('service/new.html.twig', array(
            'entity' => $service,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Service entity.
     *
     * @Route("/{slugname}", name="service_show", methods={"GET"})
     *
     */
    public function show(Service $service)
    {
        $deleteForm = $this->createDeleteForm($service->getId());

        return $this->render('service/show.html.twig', array(
            'entity' => $service,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Service entity.
     *
     * @Route("/{slugname}/edit", name="service_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Service $service)
    {
        $editForm = $this->createForm(ServiceType::class, $service)
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Le service a bien été modifié.');

            return $this->redirectToRoute('service_show', array('slugname' => $service->getSlugname()));
        }

        return $this->render('service/edit.html.twig', array(
            'entity' => $service,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Service entity.
     *
     * @Route("/{id}", name="service_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Service::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Service entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'Le service a bien été supprimé.');
        }

        return $this->redirectToRoute('service');
    }

    /**
     * Creates a form to delete a Service entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('service_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array(
                'label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
