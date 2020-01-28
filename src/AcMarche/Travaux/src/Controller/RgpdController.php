<?php

namespace AcMarche\Travaux\Controller;

use AcMarche\Travaux\Entity\Security\Group;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 *@Route("/rgpd")
 * @IsGranted("ROLE_TRAVAUX")
 */
class RgpdController extends AbstractController
{
    /**
     * @Route("/", name="rgpd")
     *
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $groupes = $em->getRepository(Group::class)->findAll();

        return $this->render('rgpd/index.html.twig', ['groupes' => $groupes]);
    }
}
