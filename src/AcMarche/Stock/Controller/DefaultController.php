<?php


namespace AcMarche\Stock\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Stock\Controller
 *
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="stock_home")
     * @IsGranted("ROLE_TRAVAUX_STOCK")
     */
    public function index()
    {
        return $this->render('stock/default/index.html.twig');
    }
}
