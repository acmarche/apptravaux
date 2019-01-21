<?php

namespace AcMarche\Travaux\Security;

use AcMarche\Travaux\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class FormLoginAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var StaffLdap
     */
    private $staffLdap;

    public function __construct(
        UserRepository $userRepository,
        RouterInterface $router,
        StaffLdap $staffLdap,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->staffLdap = $staffLdap;
    }

    public function supports(Request $request)
    {
        if ($request->getPathInfo() === '/login_check' && $request->isMethod('POST')) {
            return true;
        }

        return false;
    }

    public function getCredentials(Request $request)
    {
        return [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'token' => $request->request->get('_csrf_token'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['username'];
        $user = null;

        try {
            return $this->userRepository->loadUserByUsername($username);
        } catch (NonUniqueResultException $e) {
        }

        return null;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $token = $credentials['token'];

        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $token))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
        }

        $entry = $this->staffLdap->getEntry($user->getUsername());

        if ($entry instanceof Entry) {
            $dn = $entry->getDn();

            try {
                $this->staffLdap->bind($dn, $credentials['password']);

                return true;
            } catch (\Exception $exception) {
                //throw new BadCredentialsException($exception->getMessage());
            }
        }

        //try check password in db
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        if (!$targetPath) {
            $targetPath = $this->router->generate('homepage');
        }

        return new RedirectResponse($targetPath);
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('travaux_login');
    }
}
