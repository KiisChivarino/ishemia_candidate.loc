<?php

namespace App\Controller;

use App\Repository\NotificationConfirmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfirmNotificationController
 * @package App\Controller
 */
class ConfirmNotificationController extends AbstractController
{
    /**
     * @Route("/confirmNotification/{code}", name="confirm_notification")
     * @param $code
     * @param NotificationConfirmRepository $notificationConfirmRepository
     * @return Response
     */
    public function index($code, NotificationConfirmRepository $notificationConfirmRepository): Response
    {
        $notificationConfirm = $notificationConfirmRepository->findOneBy(['emailCode' => $code]);
        if (!$notificationConfirm) {
            return new Response('Страница ошибки из-за невалидного кода');
        }

        if ($notificationConfirm->getIsConfirmed()) {
            return new Response('Действие уже было подтверждено');
        }

        $notificationConfirm->setIsConfirmed(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($notificationConfirm);
        $em->flush();

        return new Response('Дейcтвие успешно');
    }
}
