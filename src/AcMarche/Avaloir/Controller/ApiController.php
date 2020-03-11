<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\Entity\AvaloirNew;
use AcMarche\Avaloir\Repository\AvaloirNewRepository;
use AcMarche\Stock\Service\Logger;
use AcMarche\Stock\Service\SerializeApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package AcMarche\Avaloir\Controller
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @var AvaloirNewRepository
     */
    private $avaloirRepository;
    /**
     * @var SerializeApi
     */
    private $serializeApi;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        AvaloirNewRepository $avaloirRepository,
        SerializeApi $serializeApi,
        Logger $logger
    ) {
        $this->avaloirRepository = $avaloirRepository;
        $this->serializeApi = $serializeApi;
        $this->logger = $logger;
    }

    /**
     * @Route("/all")
     */
    public function index()
    {
        $avaloirs = $this->serializeApi->serializeAvaloirs($this->avaloirRepository->findAll());

        $data = ['avaloirs' => $avaloirs];

        return new JsonResponse($avaloirs);
    }

    /**
     * @param Avaloir $avaloir
     * @param int $quantite
     * @Route("/update/{id}")
     * @return JsonResponse
     */
    public function update(Avaloir $avaloir, Request $request)
    {
        $data = $request->request->get('avaloir');
        $data = json_decode($request->getContent(), true);
        $avaloir = new AvaloirNew();
        $avaloir->setLatitude($data['latitude']);
        $avaloir->setLongitude($data['longitude']);
        $this->avaloirRepository->persist($avaloir);
        $this->avaloirRepository->flush();

        //$date = \DateTime::createFromFormat('Y-m-d', $dateNettoyage);
        //$avaloir->setUpdatedAt($date);
        //$this->avaloirRepository->flush();

        // $this->logger->log($avaloir, $quantite);

        $data = ['error' => 0, 'message' => $data, 'avaloir' => $data];
        return new JsonResponse($data);
    }

    /**
     * @param Avaloir $avaloir
     * @param int $quantite
     * @Route("/clean/{id}/{date}")
     * @return JsonResponse
     */
    public function clean(int $id, string $date)
    {
        $avaloir = $this->avaloirRepository->find($id);
        if (!$avaloir) {
            $data = ['error' => 404, 'message' => "Avaloir non trouvé", 'avaloir' => $avaloir];
            return new JsonResponse($data);
        }

        $dateNettoyage = \DateTime::createFromFormat('Y-m-d', $date);
        $avaloir->setDescription($date);
        $this->avaloirRepository->flush();

        // $this->logger->log($avaloir, $quantite);

        $data = ['error' => 0, 'message' => "ok", 'avaloir' => $avaloir];
        return new JsonResponse($data);
    }

    /**
     * @param Avaloir $avaloir
     * @param int $quantite
     * @Route("/photo/{id}")
     * @return JsonResponse
     */
    public function photo(int $id,Request $request)
    {
        $avaloir = $this->avaloirRepository->find($id);
        if (!$avaloir) {
            $data = ['error' => 404, 'message' => "Avaloir non trouvé", 'avaloir' => $avaloir];
            return new JsonResponse($data);
        }

        /**
         * @var UploadedFile $image
         */
        $image  = $request->request->get('image');

        // $this->logger->log($avaloir, $quantite);

        $data = ['error' => 0, 'message' => $image->getClientMimeType(), 'avaloir' => $avaloir];
        return new JsonResponse($data);
    }


}