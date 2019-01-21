<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Service\TravauxUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Afficher la bare de notification pour les interventions
 * en attentes pour l'auteur ou l'administrateur
 * Appeler depuis le fichier base.html
 * @Route("/notification")
 * @IsGranted("ROLE_TRAVAUX")
 */
class NotificationController extends AbstractController
{
    /**
     * @var TravauxUtils
     */
    private $travauxUtils;

    public function __construct(TravauxUtils $travauxUtils)
    {
        $this->travauxUtils = $travauxUtils;
    }

    public function index()
    {
        $interventions = $this->travauxUtils->getInterventionsEnAttentes();
        $reportees = $this->travauxUtils->getInterventionsReportees();

        return $this->render('notification/index.html.twig', array(
            'interventions' => $interventions,
            'reportees' => $reportees,
        ));
    }

    /**
     * @Route("/reporte", name="intervention_reporte", methods={"GET"})
     *
     *
     */
    public function reporte()
    {
        $reportees = $this->travauxUtils->getInterventionsReportees();

        return $this->render('notification/reporte.html.twig', array(
            'interventions' => $reportees,
        ));
    }
}
