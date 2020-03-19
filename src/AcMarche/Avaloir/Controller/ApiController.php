<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\AvaloirNew;
use AcMarche\Avaloir\Entity\DateNettoyage;
use AcMarche\Avaloir\Repository\AvaloirNewRepository;
use AcMarche\Avaloir\Repository\DateNettoyageRepository;
use AcMarche\Stock\Service\Logger;
use AcMarche\Stock\Service\SerializeApi;
use AcMarche\Travaux\Elastic\ElasticSearch;
use AcMarche\Travaux\Elastic\ElasticServer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
    private $avaloirNewRepository;
    /**
     * @var SerializeApi
     */
    private $serializeApi;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var DateNettoyageRepository
     */
    private $dateNettoyageRepository;
    /**
     * @var ElasticSearch
     */
    private $elasticSearch;
    /**
     * @var ElasticServer
     */
    private $elasticServer;

    public function __construct(
        AvaloirNewRepository $avaloirNewRepository,
        DateNettoyageRepository $dateNettoyageRepository,
        SerializeApi $serializeApi,
        Logger $logger,
        ElasticSearch $elasticSearch,
        ElasticServer $elasticServer
    ) {
        $this->avaloirNewRepository = $avaloirNewRepository;
        $this->serializeApi = $serializeApi;
        $this->logger = $logger;
        $this->dateNettoyageRepository = $dateNettoyageRepository;
        $this->elasticSearch = $elasticSearch;
        $this->elasticServer = $elasticServer;
    }

    /**
     * @Route("/all")
     */
    public function index()
    {
        $avaloirs = $this->serializeApi->serializeAvaloirs($this->avaloirNewRepository->findAll());

        return new JsonResponse($avaloirs);
    }

    /**
     * @Route("/dates")
     */
    public function dates()
    {
        $dates = $this->serializeApi->serializeDates($this->dateNettoyageRepository->findForNew());

        return new JsonResponse($dates);
    }

    /**
     * @Route("/insert", format="json")
     * @return JsonResponse
     */
    public function insert(Request $request)
    {
        $coordinatesJson = $request->request->get('coordinates');

        try {
            $data = json_decode($coordinatesJson, true);
            $avaloir = new AvaloirNew();
            $avaloir->setLatitude($data['latitude']);
            $avaloir->setLongitude($data['longitude']);
            $this->avaloirNewRepository->persist($avaloir);
            $this->avaloirNewRepository->flush();
        } catch (\Exception $exception) {
            $data = [
                'error' => 0,
                'message' => 'Avaloir non insérer dans la base de données',
                'avaloir' => $exception->getMessage()
            ];
            return new JsonResponse($data);
        }

        $result = $this->uploadImage($avaloir, $request);

        if ($result['error'] > 0) {
            return new JsonResponse($result);
        }

        try {
            $result = $this->elasticServer->updateData($avaloir);
            //$this->elasticServer->getClient()->indices()->refresh();
            $data = [
                'error' => 0,
                'elastic' => $result,
                'message' => 'ok',
                'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)
            ];
            return new JsonResponse($data);
        } catch (\Exception $e) {
            $data = [
                'error' => 1,
                'message' => $e->getMessage(),
                'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)
            ];
            return new JsonResponse($data);
        }
    }

    /**
     * @param int $id
     * @Route("/update/{id}")
     * @return JsonResponse
     */
    public function update(int $id, Request $request)
    {
        $avaloir = $this->avaloirNewRepository->find($id);
        if (!$avaloir) {
            $data = [
                'error' => 404,
                'message' => "Avaloir non trouvé",
                'avaloir' => null
            ];
            return new JsonResponse($data);
        }

        $data = json_decode($request->getContent(), true);

        $this->avaloirNewRepository->persist($avaloir);

        $data = ['error' => 0, 'message' => $data, 'avaloir' => $this->serializeApi->serializeAvaloir($data)];
        return new JsonResponse($data);
    }

    /**
     * @param AvaloirNew $avaloir
     * @param int $quantite
     * @Route("/clean/{id}/{dateString}")
     * @return JsonResponse
     */
    public function addCleaning(int $id, string $dateString)
    {
        $avaloir = $this->avaloirNewRepository->find($id);
        if (!$avaloir) {
            $data = [
                'error' => 404,
                'message' => "Avaloir non trouvé",
                'avaloir' => null
            ];
            return new JsonResponse($data);
        }

        $date = \DateTime::createFromFormat('Y-m-d', $dateString);

        if ($this->dateNettoyageRepository->findOneBy(['avaloirNew' => $avaloir, 'jour' => $date])) {
            return new JsonResponse(['error' => 1, 'message' => "Un nettoyage existe à cette date"]);
        }

        $dateNettoyage = new DateNettoyage();
        $dateNettoyage->setAvaloirNew($avaloir);
        $dateNettoyage->setJour($date);
        $dateNettoyage->setUpdatedAt($date);
        $dateNettoyage->setCreatedAt($date);

        $avaloir->addDate($dateNettoyage);

        $this->dateNettoyageRepository->persist($dateNettoyage);
        $this->dateNettoyageRepository->flush();

        $data = ['error' => 0, 'message' => "ok", 'date' => $this->serializeApi->serializeDate($dateNettoyage)];
        return new JsonResponse($data);
    }

    public function uploadImage(AvaloirNew $avaloir, Request $request)
    {
        /**
         * @var UploadedFile $image
         */
        $image = $request->files->get('image');

        if (!$image instanceof UploadedFile) {
            return
                [
                    'error' => 1,
                    'message' => 'Upload raté',
                    'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)
                ];
        }

        if ($image->getError()) {
            return
                [
                    'error' => 1,
                    'message' => $image->getErrorMessage(),
                    'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)
                ];
        }

        if ($image instanceof UploadedFile) {
            $this->upload($avaloir, $image);
        }

        return [];
    }

    private function upload(AvaloirNew $avaloir, UploadedFile $image)
    {
        $name = 'aval-' . $avaloir->getId() . '.jpg';
        try {
            $image->move(
                $this->getParameter('ac_marche_avaloir.upload.directory') . DIRECTORY_SEPARATOR . $avaloir->getId(),
                $name
            );
        } catch (FileException $e) {
            return [
                'error' => 1,
                'message' => $image->getErrorMessage(),
                'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)
            ];
        }

        $avaloir->setImageName($name);
        $this->avaloirNewRepository->flush();
        return ['error' => 0, 'message' => $name, 'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)];
    }

    /**
     *
     * @Route("/search")
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $latitude = number_format($data['latitude'], 10);
        $longitude = number_format($data['longitude'], 10);
        $distance = (string)$data['distance'];

        if (!$latitude || !$longitude || !$distance) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Latitude, longitude et distance obligatoire',
                    'avaloirs' => []
                ]
            );
        }

        $result = $this->elasticSearch->search($distance, $latitude, $longitude);
        $hits = $result['hits'];
        $total = $hits['total'];
        $avaloirs = [];

        foreach ($hits['hits'] as $hit) {
            $score = $hit['_score'];
            $post = $hit['_source'];
            $id = $post['id'];
            if ($avaloir = $this->avaloirNewRepository->find($id)) {
                $avaloirs[] = $this->serializeApi->serializeAvaloir($avaloir);
            }
        }

        return new JsonResponse(
            [
                'error' => 0,
                'message' => 'distance: ' . $distance . ' latitude: ' . $latitude . ' longitude: ' . $longitude . 'ok count ' . $total['value'],
                'avaloirs' => $avaloirs
            ]
        );
    }
}