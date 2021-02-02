<?php

namespace App\Controller;

use App\Services\LoggerService\LogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/logout_from_app", name="logout_from_app")
     * @param LogService $logService
     * @return RedirectResponse
     */
    public function logout(LogService $logService): RedirectResponse
    {
        if ($this->getUser() !== null)
        {
            $logger = $logService
                ->setUser($this->getUser())
                ->logLogoutEvent();
            if (!$logger) {
                $logService->getError();
                // TODO: when log fails
            }
        }

        return $this->redirectToRoute('app_logout');
    }
}
