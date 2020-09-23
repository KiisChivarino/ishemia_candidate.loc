<?php

namespace App\Security;

use App\Entity\AuthUser;
use App\Services\InfoService\AuthUserInfoService;
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
     * LoginFormAuthenticator constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     */
    public function getCredentials(Request $request)
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
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
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
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        $authUser = $this->entityManager->getRepository(AuthUser::class)->findOneBy(
            [
                'phone' => (new AuthUserInfoService())->clearUserPhone($this->getCredentials($request)['phone'])
            ]
        );
        $roleTechName = (new AuthUserInfoService())->getRoleNames($this->entityManager->getRepository(AuthUser::class)->getRoles($authUser), true);
        $roles = Yaml::parseFile('..//config/services/roles.yaml');
        foreach ($roles['parameters'] as $roleData) {
            if ($roleTechName && strpos($roleData['techName'], $roleTechName) !== false) {
                return new RedirectResponse($this->urlGenerator->generate($roleData['route']));
            } elseif ($roleData['techName'] == 'ROLE_DEVELOPER' and array_search($roleData['techName'], $authUser->getRoles()) !== false) {
                return new RedirectResponse($this->urlGenerator->generate($roleData['route']));
            }
        }
        return new RedirectResponse($this->urlGenerator->generate('patient_office_main'));
    }

    /**
     * @return string
     */
    protected
    function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
    }
}
