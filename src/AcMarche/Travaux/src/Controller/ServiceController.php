<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Entity\Service;
use AcMarche\Travaux\Form\ServiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

        return $this->render(
            '@AcMarcheTravaux/travaux/service/index.html.twig',
            array(
                'entities' => $entities,
            )
        );
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

            return $this->redirectToRoute('service_show', array('id' => $service->getId()));
        }

        return $this->render(
            '@AcMarcheTravaux/travaux/service/new.html.twig',
            array(
                'entity' => $service,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Service entity.
     *
     * @Route("/{id}", name="service_show", methods={"GET"})
     *
     */
    public function show(Service $service)
    {
        return $this->render(
            '@AcMarcheTravaux/travaux/service/show.html.twig',
            array(
                'entity' => $service,
            )
        );
    }

    /**
     * Displays a form to edit an existing Service entity.
     *
     * @Route("/{id}/edit", name="service_edit", methods={"GET","POST"})
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

            return $this->redirectToRoute('service_show', array('id' => $service->getId()));
        }

        return $this->render(
            '@AcMarcheTravaux/travaux/service/edit.html.twig',
            array(
                'entity' => $service,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Service entity.
     *
     * @Route("/{id}", name="service_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Service $service)
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {

            $em = $this->getDoctrine()->getManager();

            $em->remove($service);
            $em->flush();

            $this->addFlash('success', 'Le service a bien été supprimé.');
        }

        return $this->redirectToRoute('service');
    }

}
