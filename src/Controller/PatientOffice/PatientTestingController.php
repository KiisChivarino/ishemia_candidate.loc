<?php

namespace App\Controller\PatientOffice;

use App\Services\TemplateBuilders\PatientOffice\PatientTestingTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class PatientTestingController
 * @IsGranted("ROLE_PATIENT")
 *
 * @package App\Controller\PatientOffice
 * @Route("/patient_office/testing")
 */
class PatientTestingController extends PatientOfficeAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'patientOffice/testing/';

    /**
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @throws Exception
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new PatientTestingTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }
    /**
     * News patient testing
     * @Route("/news", name="patient_office_testing")
     */
    public function news(): Response
    {
        return $this->responseNewsList(self::TEMPLATE_PATH);
    }

    /**
     * history patient testing
     * @Route("/history", name="patient_office_testing_history")
     */
    public function history(): Response
    {
        return $this->responseHistoryList(self::TEMPLATE_PATH);
    }
}