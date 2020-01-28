<?php

namespace AcMarche\Travaux\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Default controller.
 *
 *
 */
class DefaultController extends AbstractController
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route("/", name="homepage")
     *
     */
    public function index()
    {
        if ($this->authorizationChecker->isGranted("ROLE_TRAVAUX")) {
            return $this->redirectToRoute('intervention', array(), '301');
        }
        $this->addFlash('danger', 'Vous n\'avez pas les droits suffisant pour cette application');
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/documentation", name="documentation")
     * @IsGranted("ROLE_TRAVAUX")
     */
    public function documentation()
    {
        return $this->render('@AcMarcheTravaux/travaux/default/documentation.html.twig');
    }
}
