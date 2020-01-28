<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/19
 * Time: 10:38
 */

namespace AcMarche\Travaux\Controller;

use AcMarche\Stock\Service\SerializeApi;
use AcMarche\Travaux\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class DefaultController
 * @package AcMarche\Mercredi\Api\Controller
 * @Route("/logapi")
 */
class LoginApiController extends AbstractController
{
    /**
     * @var SerializeApi
     */
    private $serializer;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(
        UserRepository $userRepository,
        SerializeApi $serializer,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     *
     * @Route("/", methods={"POST"})
     *
     */
    public function login(Request $request)
    {
        $error = [];
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        if (!$username || !$password) {

            $error['message'] = 'Champs non remplis';

            return new JsonResponse($error, 401);
        }

        $user = $this->userRepository->findOneBy(['username' => $username]);
        if ($user) {
            if ($this->userPasswordEncoder->isPasswordValid($user, $password)) {

                $user = $this->serializer->serializeUser($user);
                $this->userRepository->save();

                return new JsonResponse($user);
            }

            $error['message'] = 'Mauvais mot de passe';

            return new JsonResponse($error, 401);

        }
        $error['message'] = 'Utilisateur non trouvé';

        return new JsonResponse($error, 401);
    }


}