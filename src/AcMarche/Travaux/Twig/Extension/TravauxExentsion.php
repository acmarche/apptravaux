<?php

namespace AcMarche\Travaux\Twig\Extension;

use AcMarche\Travaux\Entity\Document;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

class TravauxExentsion extends \Twig_Extension
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag, RouterInterface $router)
    {
        $this->router = $router;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @Override
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('apptravaux_download', array($this, 'downloader')),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('routeExists', array($this, 'routeExists')),
        );
    }

    public function downloader(Document $document)
    {
        $this->path = $this->parameterBag->get('ac_marche_travaux.download.directory');
        $intervention = $document->getIntervention();
        $directory = $this->path."/".$intervention->getId();
        return $directory.'/'.$document->getFileName();
    }

    public function routeExists($name)
    {
        return (null === $this->router->getRouteCollection()->get($name)) ? false : true;
    }
}
