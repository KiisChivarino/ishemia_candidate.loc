<?php

namespace App\Controller\PatientOffice;

use App\Services\TemplateBuilders\PatientOffice\NotificationTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class PatientNotificationController
 * @IsGranted("ROLE_PATIENT")
 *
 * @package App\Controller\PatientOffice
 * @Route("/patient_office/notification")
 */
class NotificationController extends PatientOfficeAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'patientOffice/notification/';

    /**
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @throws Exception
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new NotificationTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * New notifications of patient
     * @Route("/news", name="patient_office_notification_news")
     */
    public function news(): Response
    {
        return $this->responseNewsList(self::TEMPLATE_PATH);
    }

    /**
     * Old patient notifications
     * @Route("/history", name="patient_office_notification_history")
     */
    public function history(): Response
    {
        return $this->responseHistoryList(self::TEMPLATE_PATH);
    }
}
