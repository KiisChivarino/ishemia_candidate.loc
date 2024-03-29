<?php

namespace App\Security;

use App\Entity\AuthUser;
use App\Repository\UserRepository;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\LoggerService\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Yaml\Yaml;

/**
 * Class LoginFormAuthenticator
 *
 * @package App\Security
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    /**
     * @var array
     */
    private $roles;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param array $roles
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        array $roles
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->roles = $roles;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     */
    public function getCredentials(Request $request): array
    {
        $credentials = [
            'phone' => $request->request->get('phone'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['phone']
        );
        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        $user = $this->entityManager->getRepository(AuthUser::class)->findOneBy(
            [
                'phone' => (new AuthUserInfoService())->clearUserPhone($credentials['phone'])
            ]
        );
        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Телефон не найден');
        }
        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param $credentials
     * @return string|null
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     *
     * @return RedirectResponse|Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $authUserInfoService = new AuthUserInfoService();
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(AuthUser::class);
        /** @var AuthUser $authUser */
        $authUser = $userRepository->findOneBy(
            [
                'phone' => $authUserInfoService->clearUserPhone($this->getCredentials($request)['phone'])
            ]
        );
        $log = new LogService($this->entityManager);
        $logger = $log
            ->setUser($authUser)
            ->logLoginEvent();
        if (!$logger) {
            $log->getError();
            // TODO:  when creating log fails
        }
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        $roleTechName = $authUserInfoService->getRoleNames($userRepository->getRoles($authUser), true);
        foreach ($this->roles as $roleData) {
            if ($roleTechName && strpos($roleData['techName'], $roleTechName) !== false) {
                return new RedirectResponse($this->urlGenerator->generate($roleData['route']));
            } elseif (array_search($roleData['techName'], $authUser->getRoles()) !== false) {
                return new RedirectResponse($this->urlGenerator->generate($roleData['route']));
            }
        }
        return new RedirectResponse($this->urlGenerator->generate('patient_office_main'));
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('app_login');
    }
}
