<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Entity\Intervention;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Export controller.
 *
 * @Route("/export")
 * @IsGranted("ROLE_TRAVAUX")
 */
class ExportController extends AbstractController
{
    /**
     * @var Pdf
     */
    private $pdf;

    public function __construct(Pdf $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     *
     * @Route("/pdf/{archive}", name="export_pdf", methods={"GET"})
     *
     */
    public function pdf(Request $request, $archive = false)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $args = [];

        if ($archive) {
            if ($session->has("intervention_archive_search")) {
                $args = unserialize($session->get("intervention_archive_search"));
            }
        } elseif ($session->has("intervention_search")) {
            $args = unserialize($session->get("intervention_search"));
        }

        $interventions = $em->getRepository(Intervention::class)->search(
            $args
        );

        $html = $this->renderView(
            'travaux/pdf/head.html.twig',
            array(
                'title' => 'Liste des interventions',
            )
        );

        foreach ($interventions as $intervention) {
            $html .= $this->renderView(
                'travaux/pdf/panel.html.twig',
                array(
                    'entity' => $intervention,
                    'pdf' => true,
                )
            );
        }

        $html .= $this->renderView('travaux/pdf/foot.html.twig', array());

        $name = 'interventions';

        $this->pdf->setOption('footer-right', '[page]/[toPage]');

        return new Response(
            $this->pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$name.'.pdf"',
            )
        );
    }
}
