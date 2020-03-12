<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\Entity\AvaloirNew;
use AcMarche\Avaloir\Repository\AvaloirNewRepository;
use AcMarche\Stock\Service\Logger;
use AcMarche\Stock\Service\SerializeApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Storage\FileSystemStorage;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

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
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

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

        $data = ['error' => 0, 'message' => $data, 'avaloir' => $this->serializeApi->serializeAvaloir($data)];
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
            $data = [
                'error' => 404,
                'message' => "Avaloir non trouvé",
                'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)
            ];
            return new JsonResponse($data);
        }

        $dateNettoyage = \DateTime::createFromFormat('Y-m-d', $date);
        $avaloir->setDescription($date);
        $this->avaloirRepository->flush();

        // $this->logger->log($avaloir, $quantite);

        $data = ['error' => 0, 'message' => "ok", 'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)];
        return new JsonResponse($data);
    }

    /**
     * @param Avaloir $avaloir
     * @param int $quantite
     * @Route("/photo/{id}")
     * @return JsonResponse
     */
    public function photo(int $id, Request $request)
    {
        $avaloir = $this->avaloirRepository->find($id);
        if (!$avaloir) {
            $data = [
                'error' => 404,
                'message' => "Avaloir non trouvé",
                'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)
            ];
            return new JsonResponse($data);
        }

        /**
         * @var UploadedFile $image
         */
        $image = $request->files->get('image');

        if (!$image instanceof UploadedFile) {
            return new JsonResponse(
                ['error' => 1, 'message' => 'Upload raté', 'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)]
            );
        }

        if ($image->getError()) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => $image->getErrorMessage(),
                    'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)
                ]
            );
        }

        if (!$image instanceof UploadedFile) {
            return new JsonResponse(
                [
                    'error' => 0,
                    'message' => $image->getClientMimeType(),
                    'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)
                ]
            );
        }

        return new JsonResponse($this->upload($avaloir, $image));
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
            return ['error' => 1, 'message' => $image->getErrorMessage(), 'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)];
        }

        $avaloir->setImageName($name);
        return ['error' => 0, 'message' => $name, 'avaloir' => $this->serializeApi->serializeAvaloir($avaloir)];
    }


}