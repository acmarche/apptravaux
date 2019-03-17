<?php


namespace AcMarche\Avaloir\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @IsGranted("ROLE_TRAVAUX_AVALOIR")
     */
    public function index()
    {
        return $this->render('avaloir/default/index.html.twig');
    }
}
