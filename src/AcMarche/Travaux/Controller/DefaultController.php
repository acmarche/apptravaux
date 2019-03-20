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

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/documentation", name="documentation")
     * @IsGranted("ROLE_TRAVAUX")
     */
    public function documentation()
    {
        return $this->render('travaux/default/documentation.html.twig');
    }
}
