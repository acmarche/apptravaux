<?php


namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\Form\LocalisationType;
use AcMarche\Avaloir\Location\LocationUpdater;
use AcMarche\Avaloir\Repository\AvaloirRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LocalisationController
 * @package AcMarche\Avaloir\Controller
 * @Route("/localisation")
 */
class LocalisationController extends AbstractController
{
    /**
     * @var AvaloirRepository
     */
    private $avaloirRepository;
    /**
     * @var LocationUpdater
     */
    private $locationUpdater;

    public function __construct(AvaloirRepository $avaloirRepository, LocationUpdater $locationUpdater)
    {
        $this->avaloirRepository = $avaloirRepository;
        $this->locationUpdater = $locationUpdater;
    }

    /**
     * @IsGranted("ROLE_TRAVAUX_AVALOIR")
     * @Route("/{id}", name="avaloir_localisation_update", methods={"POST"})
     */
    public function update(Request $request, Avaloir $avaloir)
    {
        $form = $this->createForm(LocalisationType::class, $avaloir);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->locationUpdater->updateRueAndLocalite($avaloir);
            $this->avaloirRepository->flush();
            $this->addFlash("success", "La situation a bien été modifiée");
        }

        return $this->redirectToRoute('avaloir_show', ['id' => $avaloir->getId()]);
    }
}
