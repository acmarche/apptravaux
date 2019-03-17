<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Entity\Document;
use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Form\DocumentType;
use AcMarche\Travaux\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Document controller.
 *
 * @Route("/document")
 * @IsGranted("ROLE_TRAVAUX")
 */
class DocumentController extends AbstractController
{
    /**
     * @var FileHelper
     */
    private $fileHelper;

    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * Displays a form to create a new Document entity.
     *
     * @Route("/new/{id}", name="document_new", methods={"GET","POST"})
     * @IsGranted("ROLE_TRAVAUX_ADD")
     *
     */
    public function new(Request $request, Intervention $intervention)
    {
        $document = new Document();
        $document->setIntervention($intervention);

        $form = $this->createForm(DocumentType::class, $document)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $files = $form->getData()->getFiles();

            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $fileName = md5(uniqid()).'.'.$file->guessClientExtension();

                    try {
                        $mime = $file->getMimeType();
                        $this->fileHelper->uploadFile(
                            $intervention,
                            $file,
                            $fileName
                        );
                        $document = new Document();
                        $document->setIntervention($intervention);
                        $document->setFileName($fileName);
                        $document->setMime($mime);
                        $document->setUpdatedAt(new \DateTime('now'));
                        $em->persist($document);
                        $em->flush();
                        $this->addFlash('success', 'Le document a bien été créé.');
                    } catch (FileException $error) {
                        $this->addFlash('danger', $error->getMessage());
                    }
                }
            }

            return $this->redirectToRoute('intervention_show', array('id' => $intervention->getId()));
        }

        return $this->render('travaux/document/new.html.twig', array(
            'entity' => $document,
            'intervention' => $intervention,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Document entity.
     *
     * @Route("/{id}", name="document_show", methods={"GET"})
     *
     */
    public function show(Document $document)
    {
        $deleteForm = $this->createDeleteForm($document->getId());

        return $this->render('travaux/document/show.html.twig', array(
            'entity' => $document,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Document entity.
     *
     * @Route("/{id}", name="document_delete", methods={"DELETE"})
     * @IsGranted("ROLE_TRAVAUX_ADD")
     */
    public function delete(Request $request, Document $document)
    {
        $form = $this->createDeleteForm($document->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $intervention = $document->getIntervention();

            try {
                $this->fileHelper->deleteOneDoc($document);
            } catch (IOException $exception) {
                $this->addFlash("danger", "Erreur de la suppression du document : ".$exception->getMessage());
            }

            $em->remove($document);
            $em->flush();

            $this->addFlash('success', 'Le document a bien été supprimé.');

            return $this->redirectToRoute('intervention_show', array('id' => $intervention->getId()));
        }

        return $this->redirectToRoute('intervention');
    }

    /**
     * Creates a form to delete a Document entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('document_delete', array('id' => $id)))
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
