<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Services\InfoService\AuthUserInfoService;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Yaml\Yaml;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param UserRepository $userRepository
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($this->getUser()) {
            $roleTechName = AuthUserInfoService::getRoleNames($userRepository->getRoles($this->getUser()), true);
            $roles = Yaml::parseFile('..//config/services/roles.yaml');
            foreach ($roles['parameters']['roles'] as $roleData) {
                if ($roleTechName && strpos($roleData['techName'], $roleTechName) !== false) {
                    return $this->redirectToRoute($roleData['route']);
                }elseif (array_search($roleData['techName'], $this->getUser()->getRoles()) !== false) {
                    return $this->redirectToRoute($roleData['route']);
                }
            }
            return $this->redirectToRoute('patient_office_main');
        }
        return $this->render(
            'security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error
            ]
        );
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
